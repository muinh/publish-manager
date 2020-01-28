<?php

namespace App\Service;

use App\AwsSqs\SqsQueuesBag;
use App\Bags\{MessageBag, RequestFieldsBag};
use App\Event\SeoTagsEvent;
use App\Model\ApiResponse;
use App\Service\ElasticSearch\Repository\SeoTagTypeRepository;
use App\SystemEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\{RequestStack, Response};

/**
 * Class SeoTagService
 *
 * @package App\Service
 */
class SeoTagService
{
    /**
     * @var string
     */
    private $avalonAdminApiSeoTagsPublishedUri;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SeoTagTypeRepository
     */
    private $seoTagTypeRepository;

    /**
     * @var HttpClientAdapter
     */
    private $httpClientAdapter;

    /**
     * SeoTagService constructor.
     *
     * @codeCoverageIgnore
     *
     * @param string $avalonAdminApiSeoTagsPublishedUri
     * @param RequestStack $requestStack
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param SeoTagTypeRepository $seoTagTypeRepository
     * @param HttpClientAdapter $httpClientAdapter
     */
    public function __construct(
        string $avalonAdminApiSeoTagsPublishedUri,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        SeoTagTypeRepository $seoTagTypeRepository,
        HttpClientAdapter $httpClientAdapter
    ) {
        $this->avalonAdminApiSeoTagsPublishedUri = $avalonAdminApiSeoTagsPublishedUri;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->seoTagTypeRepository = $seoTagTypeRepository;
        $this->httpClientAdapter = $httpClientAdapter;
    }

    /**
     * Handle publish seo tags request.
     *
     * @return ApiResponse
     */
    public function handlePublishSeoTagsRequest() : ApiResponse
    {
        $seoTagsData = $this->requestStack->getCurrentRequest()->request
            ->get(RequestFieldsBag::PUBLISH_SEO_TAGS_DATA_FIELD);

        $apiResponse = new ApiResponse();

        if ($seoTagsData !== null && \is_array($seoTagsData)) {
            $fieldsToCheck = ['id', 'name'];

            if ($this->isSeoTagsValid($seoTagsData, $fieldsToCheck) === false) {
                $message = sprintf(MessageBag::SEO_TAG_REQUIRED_FIELDS_MISSING, implode(',', $fieldsToCheck));

                $this->logger->critical($message, ['request_data' => $seoTagsData]);

                return $apiResponse
                    ->setContent(['message' => $message, 'request_data' => $seoTagsData])
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            $event = new SeoTagsEvent($seoTagsData);
            $this->eventDispatcher->dispatch(SystemEvents::PUBLISH_SEO_TAGS_EVENT, $event);

            if (!$event->isPublishedToQueue()) {
                $apiResponse
                    ->setContent(MessageBag::FAILED_TO_PUBLISH_SEO_TAGS_TO_QUEUE)
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            return $apiResponse;
        }

        return $apiResponse
            ->setContent(MessageBag::SEO_TAGS_NOT_RECEIVED)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Handle publish seo tags queue event.
     *
     * @param array $seoTagsData
     * @return bool Is successful
     */
    public function handlePublishSeoTagsEvent(array $seoTagsData) : bool
    {
        if (\is_array($seoTagsData)) {
            try {
                foreach ($seoTagsData as $seoTagData) {
                    $this->seoTagTypeRepository->execPut(
                        $seoTagData,
                        $this->getSeoTagTypeId($seoTagData)
                    );
                }

                $this->handleOldSeoTags($seoTagsData);

                $event = new SeoTagsEvent($seoTagsData);
                $this->eventDispatcher->dispatch(SystemEvents::PUBLISHED_SEO_TAGS_EVENT, $event);

                return true;
            } catch (\Throwable $e) {
                $this->logger->critical(MessageBag::FAILED_TO_ADD_SEO_TAGS_DATA_TO_INDEX, [
                    'error_message' => $e->getMessage(),
                    'data' => json_encode($seoTagsData)
                ]);
            }
        } else {
            $this->logger->critical(MessageBag::SEO_TAGS_DATA_IS_BROKEN, [
                'queue' => SqsQueuesBag::PUBLISH_SEO_TAGS,
                'data' => $seoTagsData,
            ]);
        }

        return false;
    }

    /**
     * Handle published seo tags consumer.
     *
     * @param array $seoTagsData
     * @return bool Is successful
     */
    public function handlePublishedSeoTagsEvent(array $seoTagsData) : bool
    {
        $fieldsToCheck = ['id'];

        if ($this->isSeoTagsValid($seoTagsData, $fieldsToCheck) === false) {
            $this->logger->critical(
                sprintf(MessageBag::SEO_TAG_REQUIRED_FIELDS_MISSING, implode(',', $fieldsToCheck)),
                [
                    'queue' => SqsQueuesBag::PUBLISHED_SEO_TAGS,
                    'data' => $seoTagsData,
                ]
            );

            return false;
        }

        $seoTagsIds = array_column($seoTagsData, 'id');
        $dataToSend = [RequestFieldsBag::PUBLISHED_SEO_TAGS_IDS_FIELD => $seoTagsIds];

        try {
            $response = $this->httpClientAdapter->post($this->avalonAdminApiSeoTagsPublishedUri, $dataToSend);

            if ($response->getStatusCode() === Response::HTTP_OK) {
                $this->logger->info($response->getBody()->getContents());

                return true;
            }

            $this->logger->critical($response->getReasonPhrase(), [
                'status_code' => $response->getStatusCode(),
                'avalon_response' => $response->getBody()->getContents(),
            ]);
        } catch (\Throwable $e) {
            $this->logger->critical(MessageBag::FAILED_TO_SEND_SEO_TAGS_PUBLISHED_RESPONSE, [
                'error_message' => $e->getMessage(),
                'request_uri' => $this->avalonAdminApiSeoTagsPublishedUri,
                'request_data' => $dataToSend,
            ]);
        }

        return false;
    }

    /**
     * Handle old seo tags.
     *
     * @param array $newSeoTags
     */
    private function handleOldSeoTags(array $newSeoTags)
    {
        $newSeoTagsIds = array_column($newSeoTags, 'id');
        $seoTags = $this->seoTagTypeRepository->getAll();

        $oldSeoTags = array_filter($seoTags, function ($item) use ($newSeoTagsIds) {
            return !\in_array($item['id'], $newSeoTagsIds, true);
        });

        foreach ($oldSeoTags as $oldSeoTag) {
            $this->seoTagTypeRepository->execDeleteByTypeId($this->getSeoTagTypeId($oldSeoTag));
        }

        foreach ($seoTags as $seoTag) {
            $this->seoTagTypeRepository->execDeleteByTypeId($this->getOldSeoTagTypeId($seoTag));
        }
    }

    /**
     * Check is seo tags valid.
     *
     * @param array $seoTagsData
     * @param array $fields
     * @return bool
     */
    private function isSeoTagsValid(array $seoTagsData, array $fields = []) : bool
    {
        foreach ($seoTagsData as $seoTagData) {
            foreach ($fields as $field) {
                if (array_key_exists($field, $seoTagData) === false || empty($seoTagData[$field])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get seo tag type id.
     *
     * @param array $seoTag
     * @return string
     */
    private function getSeoTagTypeId(array $seoTag) : string
    {
        return md5($seoTag['id']);
    }

    /**
     * Get seo tag type id.
     *
     * @param array $seoTag
     * @return string
     */
    private function getOldSeoTagTypeId(array $seoTag) : string
    {
        return md5($seoTag['name']);
    }
}

<?php

namespace App\Service;

use App\AwsSqs\SqsQueuesBag;
use App\Bags\{MessageBag, RequestFieldsBag};
use App\Event\SeoTagsGroupsEvent;
use App\Model\ApiResponse;
use App\Service\ElasticSearch\Repository\SeoTagsGroupTypeRepository;
use App\SystemEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\{RequestStack, Response};

/**
 * Class SeoTagsGroupsService
 *
 * @package App\Service
 */
class SeoTagsGroupsService
{
    /**
     * @var string
     */
    private $avalonAdminApiSeoTagsGroupsPublishedUri;

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
     * @var SeoTagsGroupTypeRepository
     */
    private $seoTagsGroupTypeRepository;

    /**
     * @var HttpClientAdapter
     */
    private $httpClientAdapter;

    /**
     * SeoTagService constructor.
     *
     * @codeCoverageIgnore
     *
     * @param string $avalonAdminApiSeoTagsGroupsPublishedUri
     * @param RequestStack $requestStack
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param SeoTagsGroupTypeRepository $seoTagsGroupTypeRepository
     * @param HttpClientAdapter $httpClientAdapter
     */
    public function __construct(
        string $avalonAdminApiSeoTagsGroupsPublishedUri,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        SeoTagsGroupTypeRepository $seoTagsGroupTypeRepository,
        HttpClientAdapter $httpClientAdapter
    ) {
        $this->avalonAdminApiSeoTagsGroupsPublishedUri = $avalonAdminApiSeoTagsGroupsPublishedUri;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->seoTagsGroupTypeRepository = $seoTagsGroupTypeRepository;
        $this->httpClientAdapter = $httpClientAdapter;
    }

    /**
     * Handle publish seo tags groups request.
     *
     * @return ApiResponse
     */
    public function handlePublishSeoTagsGroupsRequest() : ApiResponse
    {
        $seoTagsGroupsData = $this->requestStack->getCurrentRequest()->request
            ->get(RequestFieldsBag::PUBLISH_SEO_TAGS_GROUPS_DATA_FIELD);

        $apiResponse = new ApiResponse();

        if ($seoTagsGroupsData !== null && \is_array($seoTagsGroupsData)) {
            $fieldsToCheck = ['id', 'name', 'url'];

            if ($this->isSeoTagsGroupsValid($seoTagsGroupsData, $fieldsToCheck) === false) {
                $message = sprintf(MessageBag::SEO_TAGS_GROUP_REQUIRED_FIELDS_MISSING, implode(',', $fieldsToCheck));

                $this->logger->critical($message, ['request_data' => $seoTagsGroupsData]);

                return $apiResponse
                    ->setContent(['message' => $message, 'request_data' => $seoTagsGroupsData])
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            $event = new SeoTagsGroupsEvent($seoTagsGroupsData);
            $this->eventDispatcher->dispatch(SystemEvents::PUBLISH_SEO_TAGS_GROUPS_EVENT, $event);

            if (!$event->isPublishedToQueue()) {
                $apiResponse
                    ->setContent(MessageBag::FAILED_TO_PUBLISH_SEO_TAG_GROUPS_TO_QUEUE)
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            return $apiResponse;
        }

        return $apiResponse
            ->setContent(MessageBag::SEO_TAGS_GROUPS_NOT_RECEIVED)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Handle publish seo tags groups queue event.
     *
     * @param array $seoTagsGroupsData
     * @return bool Is successful
     */
    public function handlePublishSeoTagsGroupsEvent(array $seoTagsGroupsData) : bool
    {
        if (\is_array($seoTagsGroupsData)) {
            try {
                foreach ($seoTagsGroupsData as $seoTagsGroupData) {
                    $this->seoTagsGroupTypeRepository->execPut(
                        $seoTagsGroupData,
                        $this->getSeoTagsGroupTypeId($seoTagsGroupData)
                    );
                }

                $this->handleOldSeoTagsGroups($seoTagsGroupsData);

                $event = new SeoTagsGroupsEvent($seoTagsGroupsData);
                $this->eventDispatcher->dispatch(SystemEvents::PUBLISHED_SEO_TAGS_GROUPS_EVENT, $event);

                return true;
            } catch (\Throwable $e) {
                $this->logger->critical(MessageBag::FAILED_TO_ADD_SEO_TAGS_GROUPS_DATA_TO_INDEX, [
                    'error_message' => $e->getMessage(),
                    'data' => json_encode($seoTagsGroupsData),
                ]);
            }
        } else {
            $this->logger->critical(MessageBag::SEO_TAGS_GROUPS_DATA_IS_BROKEN, [
                'queue' => SqsQueuesBag::PUBLISH_SEO_TAGS_GROUPS,
                'data' => $seoTagsGroupsData,
            ]);
        }

        return false;
    }

    /**
     * Handle published seo tags groups consumer.
     *
     * @param array $seoTagsGroupsData
     * @return bool Is successful
     */
    public function handlePublishedSeoTagsGroupsEvent(array $seoTagsGroupsData) : bool
    {
        $fieldsToCheck = ['id'];

        if ($this->isSeoTagsGroupsValid($seoTagsGroupsData, $fieldsToCheck) === false) {
            $this->logger->critical(
                sprintf(MessageBag::SEO_TAGS_GROUP_REQUIRED_FIELDS_MISSING, implode(',', $fieldsToCheck)),
                [
                    'queue' => SqsQueuesBag::PUBLISHED_SEO_TAGS_GROUPS,
                    'data' => $seoTagsGroupsData,
                ]
            );

            return false;
        }

        $seoTagsGroupsIds = array_column($seoTagsGroupsData, 'id');
        $dataToSend = [RequestFieldsBag::PUBLISHED_SEO_TAGS_GROUPS_IDS_FIELD => $seoTagsGroupsIds];

        try {
            $response = $this->httpClientAdapter->post($this->avalonAdminApiSeoTagsGroupsPublishedUri, $dataToSend);

            if ($response->getStatusCode() === Response::HTTP_OK) {
                $this->logger->info($response->getBody()->getContents());

                return true;
            }

            $this->logger->critical($response->getReasonPhrase(), [
                'status_code' => $response->getStatusCode(),
                'avalon_response' => $response->getBody()->getContents(),
            ]);
        } catch (\Throwable $e) {
            $this->logger->critical(MessageBag::FAILED_TO_SEND_SEO_TAGS_GROUPS_PUBLISHED_RESPONSE, [
                'error_message' => $e->getMessage(),
                'request_uri' => $this->avalonAdminApiSeoTagsGroupsPublishedUri,
                'request_data' => $dataToSend,
            ]);
        }

        return false;
    }

    /**
     * Handle old seo tags groups.
     *
     * @param array $newSeoTagsGroups
     */
    private function handleOldSeoTagsGroups(array $newSeoTagsGroups)
    {
        $newSeoTagsGroupsIds = array_column($newSeoTagsGroups, 'id');
        $seoTagsGroups = $this->seoTagsGroupTypeRepository->getAll();

        $oldSeoTagsGroups = array_filter($seoTagsGroups, function ($item) use ($newSeoTagsGroupsIds) {
            return !\in_array($item['id'], $newSeoTagsGroupsIds, true);
        });

        foreach ($oldSeoTagsGroups as $oldSeoTagsGroup) {
            $this->seoTagsGroupTypeRepository->execDeleteByTypeId($this->getSeoTagsGroupTypeId($oldSeoTagsGroup));
        }
    }

    /**
     * Check is seo tags groups valid.
     *
     * @param array $seoTagsGroupsData
     * @param array $fields
     * @return bool
     */
    private function isSeoTagsGroupsValid(array $seoTagsGroupsData, array $fields = []) : bool
    {
        foreach ($seoTagsGroupsData as $seoTagsGroupData) {
            foreach ($fields as $field) {
                if (array_key_exists($field, $seoTagsGroupData) === false || empty($seoTagsGroupData[$field])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get seo tags group type id.
     *
     * @param array $seoTagsGroup
     * @return string
     */
    private function getSeoTagsGroupTypeId(array $seoTagsGroup) : string
    {
        return md5($seoTagsGroup['id']);
    }
}

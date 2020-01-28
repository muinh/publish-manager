<?php

namespace App\Service;

use App\AwsSqs\SqsQueuesBag;
use App\Bags\{MessageBag, RequestFieldsBag};
use App\Event\CategoryEvent;
use App\Model\ApiResponse;
use App\Service\ElasticSearch\Repository\CategoryTypeRepository;
use App\SystemEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\{RequestStack, Response};

/**
 * Class CategoryService
 *
 * @package App\Service
 */
class CategoryService
{
    /**
     * @var string
     */
    private $avalonAdminApiCategoriesPublishedUri;

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
     * @var CategoryTypeRepository
     */
    private $categoryTypeRepository;

    /**
     * @var HttpClientAdapter
     */
    private $httpClientAdapter;

    /**
     * CategoryService constructor.
     *
     * @codeCoverageIgnore
     *
     * @param string $avalonAdminApiCategoriesPublishedUri
     * @param RequestStack $requestStack
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param CategoryTypeRepository $categoryTypeRepository
     * @param HttpClientAdapter $httpClientAdapter
     */
    public function __construct(
        string $avalonAdminApiCategoriesPublishedUri,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        CategoryTypeRepository $categoryTypeRepository,
        HttpClientAdapter $httpClientAdapter
    ) {
        $this->avalonAdminApiCategoriesPublishedUri = $avalonAdminApiCategoriesPublishedUri;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->categoryTypeRepository = $categoryTypeRepository;
        $this->httpClientAdapter = $httpClientAdapter;
    }

    /**
     * Handle publish category request.
     *
     * @return ApiResponse
     */
    public function handlePublishCategoryRequest() : ApiResponse
    {
        $categoryData = $this->requestStack->getCurrentRequest()->request
            ->get(RequestFieldsBag::PUBLISH_CATEGORY_DATA_FIELD);

        $apiResponse = new ApiResponse();

        if ($categoryData !== null && \is_array($categoryData)) {
            if ($this->isCategoryUrlExist($categoryData) === false) {
                $message = sprintf(MessageBag::CATEGORY_REQUIRED_FIELDS_MISSING, implode(',', ['url']));

                $this->logger->critical($message, [
                    'request_data' => $categoryData
                ]);

                return $apiResponse
                    ->setContent(['message' => $message, 'request_data' => $categoryData])
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            $event = new CategoryEvent($categoryData);
            $this->eventDispatcher->dispatch(SystemEvents::PUBLISH_CATEGORY_EVENT, $event);

            if (!$event->isPublishedToQueue()) {
                $apiResponse
                    ->setContent(MessageBag::FAILED_TO_PUBLISH_CATEGORY_TO_QUEUE)
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            return $apiResponse;
        }

        return $apiResponse
            ->setContent(MessageBag::CATEGORY_NOT_RECEIVED)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Handle publish category queue event.
     *
     * @param array $categoryData
     * @return bool Is successful
     */
    public function handlePublishCategoryEvent(array $categoryData) : bool
    {
        if (\is_array($categoryData) && $categoryData !== [] && array_key_exists('url', $categoryData)) {
            try {
                $this->categoryTypeRepository->execPut(
                    $categoryData,
                    $this->getCategoryElasticSearchId($categoryData)
                );

                $event = new CategoryEvent($categoryData);
                $this->eventDispatcher->dispatch(SystemEvents::PUBLISHED_CATEGORY_EVENT, $event);

                return true;
            } catch (\Throwable $e) {
                $this->logger->critical(MessageBag::FAILED_TO_ADD_CATEGORY_DATA_TO_INDEX, [
                    'error_message' => $e->getMessage(),
                    'data' => json_encode($categoryData),
                ]);
            }
        } else {
            $this->logger->critical(MessageBag::CATEGORY_DATA_IS_BROKEN, [
                'queue' => SqsQueuesBag::PUBLISH_CATEGORIES,
                'data' => $categoryData,
            ]);
        }

        return false;
    }

    /**
     * Handle published category consumer.
     *
     * @param array $categoryData
     * @return bool Is successful
     */
    public function handlePublishedCategoryEvent(array $categoryData) : bool
    {
        if (isset($categoryData['id'])) {
            $dataToSend = [RequestFieldsBag::PUBLISHED_CATEGORY_ID_FIELD => $categoryData['id']];

            try {
                $response = $this->httpClientAdapter->post($this->avalonAdminApiCategoriesPublishedUri, $dataToSend);

                if ($response->getStatusCode() === Response::HTTP_OK) {
                    $this->logger->info($response->getBody()->getContents());

                    return true;
                }

                $this->logger->critical($response->getReasonPhrase(), [
                    'status_code' => $response->getStatusCode(),
                    'avalon_response' => $response->getBody()->getContents(),
                ]);
            } catch (\Throwable $e) {
                $this->logger->critical(MessageBag::FAILED_TO_SEND_CATEGORY_PUBLISHED_RESPONSE, [
                    'error_message' => $e->getMessage(),
                    'request_uri' => $this->avalonAdminApiCategoriesPublishedUri,
                    'request_data' => $dataToSend,
                ]);
            }
        } else {
            $this->logger->critical(sprintf(MessageBag::CATEGORY_REQUIRED_FIELDS_MISSING, 'id'), [
                'queue' => SqsQueuesBag::PUBLISHED_CATEGORIES,
                'data' => $categoryData,
            ]);
        }

        return false;
    }

    /**
     * Check if category url exists.
     *
     * @param array $categoryData
     * @return bool
     */
    private function isCategoryUrlExist(array $categoryData) : bool
    {
        return !(array_key_exists('url', $categoryData) === false || trim($categoryData['url']) === '');
    }

    /**
     * Get category elastic search id.
     *
     * @param array $category
     * @return string
     */
    private function getCategoryElasticSearchId(array $category) : string
    {
        return md5($category['url']);
    }
}

<?php

namespace App\Service;

use App\AwsSqs\SqsQueuesBag;
use App\Bags\{MessageBag, RequestFieldsBag};
use App\Event\PostEvent;
use App\Model\ApiResponse;
use App\Service\ElasticSearch\Repository\PostTypeRepository;
use App\SystemEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\{RequestStack, Response};

/**
 * Class PostService
 *
 * @package App\Service
 */
class PostService
{
    /**
     * @var string
     */
    private $avalonAdminApiPostPublishedUri;

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
     * @var PostTypeRepository
     */
    private $postTypeRepository;

    /**
     * @var HttpClientAdapter
     */
    private $httpClientAdapter;

    /**
     * PostService constructor.
     *
     * @codeCoverageIgnore
     *
     * @param string $avalonAdminApiPostPublishedUri
     * @param RequestStack $requestStack
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param PostTypeRepository $postTypeRepository
     * @param HttpClientAdapter $httpClientAdapter
     */
    public function __construct(
        string $avalonAdminApiPostPublishedUri,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        PostTypeRepository $postTypeRepository,
        HttpClientAdapter $httpClientAdapter
    ) {
        $this->avalonAdminApiPostPublishedUri = $avalonAdminApiPostPublishedUri;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->postTypeRepository = $postTypeRepository;
        $this->httpClientAdapter = $httpClientAdapter;
    }

    /**
     * Handle publish post request.
     *
     * @return ApiResponse
     */
    public function handlePublishPostRequest() : ApiResponse
    {
        $postData = $this->requestStack->getCurrentRequest()->request
            ->get(RequestFieldsBag::PUBLISH_POST_DATA_FIELD);

        $apiResponse = new ApiResponse();

        if ($postData !== null && \is_array($postData)) {
            if (array_key_exists('url', $postData) === false) {
                $message = printf(MessageBag::POST_REQUIRED_FIELDS_MISSING, implode(',', ['url']));

                $this->logger->critical($message);

                return $apiResponse
                    ->setContent(sprintf(MessageBag::POST_REQUIRED_FIELDS_MISSING, implode(',', ['url'])))
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            $event = new PostEvent($postData);
            $this->eventDispatcher->dispatch(SystemEvents::PUBLISH_POST_EVENT, $event);

            if (!$event->isPublishedToQueue()) {
                $apiResponse
                    ->setContent(MessageBag::FAILED_TO_PUBLISH_POST_TO_QUEUE)
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            return $apiResponse;
        }

        return $apiResponse
            ->setContent(MessageBag::POST_NOT_RECEIVED)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Handle unpublish post request.
     *
     * @return ApiResponse
     */
    public function handleUnpublishPostRequest() : ApiResponse
    {
        $postId = $this->requestStack->getCurrentRequest()->request
            ->getInt(RequestFieldsBag::UNPUBLISH_POST_ID_FIELD);

        $apiResponse = new ApiResponse();

        if (\is_numeric($postId)) {
            $foundPostsResponse = $this->postTypeRepository
                ->execSearch(['query' => ['match' => ['id' => $postId]]]);
            $totalPosts = $foundPostsResponse['hits']['total'] ?? null;

            if ($totalPosts > 0) {
                $foundPosts = $foundPostsResponse['hits']['hits'] ?? [];

                foreach ($foundPosts as $foundPost) {
                    $this->postTypeRepository->execUpdate([
                        'id' => $foundPost['_id'],
                        'body' => ['doc' => ['isDeleted' => true]]
                    ]);
                }

                return $apiResponse;
            }

            return $apiResponse->setContent(sprintf(MessageBag::NO_POSTS_FOUNT_TO_UNPUBLISH, $postId));
        }

        return $apiResponse
            ->setContent(MessageBag::POST_ID_NOT_RECEIVED)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Handle publish post queue event.
     *
     * @param array $postData
     * @return bool Is successful
     */
    public function handlePublishPostEvent(array $postData) : bool
    {
        if (\is_array($postData) && $postData !== [] && array_key_exists('url', $postData)) {
            try {
                $postUrlHash = md5($postData['url']);

                $currentTimeStamp = (new \DateTime())->getTimestamp();
                $postData['republishedAt'] = $currentTimeStamp;
                $postData['publishedAt'] = $postData['publishedAt'] ?? $currentTimeStamp;

                $this->postTypeRepository->execPut($postData, $postUrlHash);
                $event = new PostEvent($postData);
                $this->eventDispatcher->dispatch(SystemEvents::PUBLISHED_POST_EVENT, $event);

                return true;
            } catch (\Throwable $e) {
                $this->logger->critical(MessageBag::FAILED_TO_ADD_POST_DATA_TO_INDEX, [
                    'error_message' => $e->getMessage(),
                    'post_data' => json_encode($postData),
                ]);
            }
        } else {
            $this->logger->critical(MessageBag::POST_DATA_IS_BROKEN, [
                'queue' => SqsQueuesBag::PUBLISH_POST,
                'data' => $postData,
            ]);
        }

        return false;
    }

    /**
     * Handle published post queue event.
     *
     * @param array $postData
     * @return bool Is successful
     */
    public function handlePublishedPostEvent(array $postData) : bool
    {
        if (isset($postData['id'])) {
            $dataToSend = [
                RequestFieldsBag::PUBLISHED_POST_ID_FIELD => $postData['id'],
                RequestFieldsBag::PUBLISHED_POST_PUBLISHED_AT_FIELD => $postData['publishedAt'],
                RequestFieldsBag::PUBLISHED_POST_REPUBLISHED_AT_FIELD => $postData['republishedAt'],
            ];

            try {
                $response = $this->httpClientAdapter->post($this->avalonAdminApiPostPublishedUri, $dataToSend);

                if ($response->getStatusCode() === Response::HTTP_OK) {
                    $this->logger->info($response->getBody()->getContents());

                    return true;
                }

                $this->logger->critical($response->getReasonPhrase(), [
                    'status_code' => $response->getStatusCode(),
                    'avalon_response' => $response->getBody()->getContents(),
                ]);
            } catch (\Throwable $e) {
                $this->logger->critical(MessageBag::FAILED_TO_SEND_POST_PUBLISHED_RESPONSE, [
                    'error_message' => $e->getMessage(),
                    'request_uri' => $this->avalonAdminApiPostPublishedUri,
                    'request_data' => $dataToSend,
                ]);
            }
        } else {
            $this->logger->critical(sprintf(MessageBag::POST_REQUIRED_FIELDS_MISSING, 'id'), [
                'queue' => SqsQueuesBag::PUBLISHED_POST,
                'data' => $postData,
            ]);
        }

        return false;
    }
}

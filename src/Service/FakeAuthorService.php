<?php

namespace App\Service;

use App\AwsSqs\SqsQueuesBag;
use App\Event\FakeAuthorEvent;
use App\Service\ElasticSearch\Repository\FakeAuthorTypeRepository;
use App\Bags\{MessageBag, RequestFieldsBag};
use App\Model\ApiResponse;
use App\SystemEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\{RequestStack, Response};

/**
 * Class FakeAuthorService
 *
 * @package App\Service
 */
class FakeAuthorService
{
    /**
     * @var string
     */
    private $avalonAdminApiFakeAuthorsPublishedUri;

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
     * @var FakeAuthorTypeRepository
     */
    private $fakeAuthorTypeRepository;

    /**
     * @var HttpClientAdapter
     */
    private $httpClientAdapter;

    /**
     * FakeAuthorService constructor.
     *
     * @codeCoverageIgnore
     *
     * @param string $avalonAdminApiFakeAuthorsPublishedUri
     * @param RequestStack $requestStack
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param FakeAuthorTypeRepository $fakeAuthorTypeRepository
     * @param HttpClientAdapter $httpClientAdapter
     */
    public function __construct(
        string $avalonAdminApiFakeAuthorsPublishedUri,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        FakeAuthorTypeRepository $fakeAuthorTypeRepository,
        HttpClientAdapter $httpClientAdapter
    ) {
        $this->avalonAdminApiFakeAuthorsPublishedUri = $avalonAdminApiFakeAuthorsPublishedUri;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->fakeAuthorTypeRepository = $fakeAuthorTypeRepository;
        $this->httpClientAdapter = $httpClientAdapter;
    }

    /**
     * Handle publish fake author request.
     *
     * @return ApiResponse
     */
    public function handlePublishFakeAuthorRequest() : ApiResponse
    {
        $fakeAuthorData = $this->requestStack->getCurrentRequest()->request
            ->get(RequestFieldsBag::PUBLISH_FAKE_AUTHOR_DATA_FIELD);

        $apiResponse = new ApiResponse();

        if ($fakeAuthorData !== null && \is_array($fakeAuthorData)) {
            if ($this->isHasRequiredFields($fakeAuthorData) === false) {
                $message = sprintf(MessageBag::FAKE_AUTHOR_REQUIRED_FIELDS_MISSING, implode(',', ['code']));

                $this->logger->critical($message, [
                    'request_data' => $fakeAuthorData
                ]);

                return $apiResponse
                    ->setContent(['message' => $message, 'request_data' => $fakeAuthorData])
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            $event = new FakeAuthorEvent($fakeAuthorData);
            $this->eventDispatcher->dispatch(SystemEvents::PUBLISH_FAKE_AUTHOR_EVENT, $event);

            if (!$event->isPublishedToQueue()) {
                $apiResponse
                    ->setContent(MessageBag::FAILED_TO_PUBLISH_FAKE_AUTHOR_TO_QUEUE)
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            return $apiResponse;
        }

        return $apiResponse
            ->setContent(MessageBag::FAKE_AUTHOR_NOT_RECEIVED)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Handle publish fake author queue event.
     *
     * @param array $fakeAuthorData
     * @return bool Is successful
     */
    public function handlePublishFakeAuthorEvent(array $fakeAuthorData) : bool
    {
        if (\is_array($fakeAuthorData) && $fakeAuthorData !== [] && array_key_exists('code', $fakeAuthorData)) {
            try {
                $this->fakeAuthorTypeRepository->execPut(
                    $fakeAuthorData,
                    $this->getFakeAuthorElasticSearchId($fakeAuthorData)
                );

                $event = new FakeAuthorEvent($fakeAuthorData);
                $this->eventDispatcher->dispatch(SystemEvents::PUBLISHED_FAKE_AUTHOR_EVENT, $event);

                return true;
            } catch (\Throwable $e) {
                $this->logger->critical(MessageBag::FAILED_TO_ADD_FAKE_AUTHOR_DATA_TO_INDEX, [
                    'error_message' => $e->getMessage(),
                    'data' => json_encode($fakeAuthorData),
                ]);
            }
        } else {
            $this->logger->critical(MessageBag::FAKE_AUTHOR_DATA_IS_BROKEN, [
                'queue' => SqsQueuesBag::PUBLISH_FAKE_AUTHOR,
                'data' => $fakeAuthorData,
            ]);
        }

        return false;
    }

    /**
     * Handle published fake author consumer.
     *
     * @param array $fakeAuthorData
     * @return bool Is successful
     */
    public function handlePublishedFakeAuthorEvent(array $fakeAuthorData) : bool
    {
        if (isset($fakeAuthorData['id'])) {
            $dataToSend = [RequestFieldsBag::PUBLISHED_FAKE_AUTHOR_ID_FIELD => $fakeAuthorData['id']];

            try {
                $response = $this->httpClientAdapter->post($this->avalonAdminApiFakeAuthorsPublishedUri, $dataToSend);

                if ($response->getStatusCode() === Response::HTTP_OK) {
                    $this->logger->info($response->getBody()->getContents());

                    return true;
                }

                $this->logger->critical($response->getReasonPhrase(), [
                    'status_code' => $response->getStatusCode(),
                    'avalon_response' => $response->getBody()->getContents(),
                ]);
            } catch (\Throwable $e) {
                $this->logger->critical(MessageBag::FAILED_TO_SEND_FAKE_AUTHOR_PUBLISHED_RESPONSE, [
                    'error_message' => $e->getMessage(),
                    'request_uri' => $this->avalonAdminApiFakeAuthorsPublishedUri,
                    'request_data' => $dataToSend,
                ]);
            }
        } else {
            $this->logger->critical(sprintf(MessageBag::FAKE_AUTHOR_REQUIRED_FIELDS_MISSING, 'id'), [
                'queue' => SqsQueuesBag::PUBLISHED_FAKE_AUTHOR,
                'data' => $fakeAuthorData,
            ]);
        }

        return false;
    }

    /**
     * Check if fake author code exists.
     *
     * @param array $fakeAuthorData
     * @return bool
     */
    private function isHasRequiredFields(array $fakeAuthorData) : bool
    {
        return !(array_key_exists('code', $fakeAuthorData) === false || trim($fakeAuthorData['code']) === '');
    }

    /**
     * Get fake author elastic search id.
     *
     * @param array $fakeAuthor
     * @return string
     */
    private function getFakeAuthorElasticSearchId(array $fakeAuthor) : string
    {
        return md5($fakeAuthor['code']);
    }
}
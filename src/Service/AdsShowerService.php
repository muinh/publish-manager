<?php

namespace App\Service;

use App\AwsSqs\SqsQueuesBag;
use App\Bags\{ElasticSearchParametersBag, MessageBag, RequestFieldsBag};
use App\Event\AdsShowerEvent;
use App\Model\ApiResponse;
use App\Service\ElasticSearch\Repository\AdsShowerTypeRepository;
use App\SystemEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\{RequestStack, Response};

/**
 * Class AdsShowerService
 *
 * @package App\Service
 */
class AdsShowerService
{
    /**
     * @var string
     */
    private $avalonAdminApiAdsShowerPublishedUri;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var AdsShowerTypeRepository
     */
    private $adsShowerTypeRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var HttpClientAdapter
     */
    private $httpClientAdapter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AdsShowerService constructor.
     *
     * @codeCoverageIgnore
     *
     * @param string $avalonAdminApiAdsShowerPublishedUri
     * @param RequestStack $requestStack
     * @param EventDispatcherInterface $eventDispatcher
     * @param AdsShowerTypeRepository $adsShowerTypeRepository
     * @param HttpClientAdapter $httpClientAdapter
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $avalonAdminApiAdsShowerPublishedUri,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        AdsShowerTypeRepository $adsShowerTypeRepository,
        HttpClientAdapter $httpClientAdapter,
        LoggerInterface $logger
    ) {
        $this->avalonAdminApiAdsShowerPublishedUri = $avalonAdminApiAdsShowerPublishedUri;
        $this->eventDispatcher = $eventDispatcher;
        $this->adsShowerTypeRepository = $adsShowerTypeRepository;
        $this->requestStack = $requestStack;
        $this->httpClientAdapter = $httpClientAdapter;
        $this->logger = $logger;
    }

    /**
     * Handle publish ads shower request.
     *
     * @return ApiResponse
     */
    public function handlePublishAdsShowerRequest() : ApiResponse
    {
        $adsShowerData = $this->requestStack->getCurrentRequest()->request
            ->get(RequestFieldsBag::PUBLISH_ADS_SHOWER_DATA_FIELD);

        $apiResponse = new ApiResponse();

        if ($adsShowerData !== null) {
            $event = new AdsShowerEvent($adsShowerData);
            $this->eventDispatcher->dispatch(SystemEvents::PUBLISH_ADS_SHOWER_EVENT, $event);

            if (!$event->isPublishedToQueue()) {
                $apiResponse
                    ->setContent(MessageBag::FAILED_TO_PUBLISH_ADS_SHOWER_TO_QUEUE)
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            return $apiResponse;
        }

        return $apiResponse
            ->setContent(MessageBag::ADS_SHOWER_NOT_RECEIVED)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Handle publish ads shower queue event.
     *
     * @param array $adsShowerData
     * @return bool Is successful
     */
    public function handlePublishAdsShowerEvent(array $adsShowerData) : bool
    {
        if (\is_array($adsShowerData) && $adsShowerData !== []) {
            try {
                $this->adsShowerTypeRepository->execPut(
                    $adsShowerData,
                    ElasticSearchParametersBag::ID_ADS_SHOWER
                );

                $event = new AdsShowerEvent($adsShowerData);
                $this->eventDispatcher->dispatch(SystemEvents::PUBLISHED_ADS_SHOWER_EVENT, $event);

                return true;
            } catch (\Throwable $e) {
                $this->logger->critical(MessageBag::FAILED_TO_ADD_ADS_SHOWER_DATA_TO_INDEX, [
                    'error_message' => $e->getMessage(),
                    'data' => json_encode($adsShowerData),
                ]);
            }
        } else {
            $this->logger->critical(MessageBag::ADS_SHOWER_DATA_IS_BROKEN, [
                'queue' => SqsQueuesBag::PUBLISH_ADS_SHOWER,
                'data' => $adsShowerData,
            ]);
        }

        return false;
    }

    /**
     * Handle published ads shower queue event.
     *
     * @param array $adsShowerData
     * @return bool Is successful
     */
    public function handlePublishedAdsShowerEvent(array $adsShowerData) : bool
    {
        if (isset($adsShowerData['id'])) {
            $dataToSend = [RequestFieldsBag::PUBLISHED_ADS_SHOWER_ID_FIELD => $adsShowerData['id']];

            try {
                $response = $this->httpClientAdapter->post($this->avalonAdminApiAdsShowerPublishedUri, $dataToSend);

                if ($response->getStatusCode() === Response::HTTP_OK) {
                    $this->logger->info($response->getBody()->getContents());
                } else {
                    $this->logger->critical($response->getReasonPhrase(), [
                        'status_code' => $response->getStatusCode(),
                        'avalon_response' => $response->getBody()->getContents(),
                    ]);
                }
            } catch (\Throwable $e) {
                $this->logger->critical(MessageBag::FAILED_TO_SEND_ADS_SHOWER_PUBLISHED_RESPONSE, [
                    'error_message' => $e->getMessage(),
                    'request_uri' => $this->avalonAdminApiAdsShowerPublishedUri,
                    'request_data' => $dataToSend,
                ]);
            }
        } else {
            $this->logger->critical(sprintf(MessageBag::ADS_SHOWER_REQUIRED_FIELDS_MISSING, 'id'), [
                'queue' => SqsQueuesBag::PUBLISHED_ADS_SHOWER,
                'data' => $adsShowerData,
            ]);
        }

        return false;
    }
}

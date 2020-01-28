<?php

namespace App\Service;

use App\AwsSqs\SqsQueuesBag;
use App\Bags\{ElasticSearchParametersBag, MessageBag, RequestFieldsBag};
use App\Event\AnalyticScriptEvent;
use App\Model\ApiResponse;
use App\Service\ElasticSearch\Repository\AnalyticsScriptTypeRepository;
use App\SystemEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\{RequestStack, Response};

/**
 * Class AnalyticScriptService
 *
 * @package App\Service
 */
class AnalyticScriptService
{
    /**
     * @var string
     */
    private $avalonAdminApiAnalyticScriptPublishedUri;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var AnalyticsScriptTypeRepository
     */
    private $analyticsScriptTypeRepository;

    /**
     * @var HttpClientAdapter
     */
    private $httpClientAdapter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AnalyticScriptService constructor.
     *
     * @codeCoverageIgnore
     *
     * @param string $avalonAdminApiAnalyticScriptPublishedUri
     * @param RequestStack $requestStack
     * @param EventDispatcherInterface $eventDispatcher
     * @param AnalyticsScriptTypeRepository $analyticsScriptTypeRepository
     * @param HttpClientAdapter $httpClientAdapter
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $avalonAdminApiAnalyticScriptPublishedUri,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        AnalyticsScriptTypeRepository $analyticsScriptTypeRepository,
        HttpClientAdapter $httpClientAdapter,
        LoggerInterface $logger
    ) {
        $this->avalonAdminApiAnalyticScriptPublishedUri = $avalonAdminApiAnalyticScriptPublishedUri;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->analyticsScriptTypeRepository = $analyticsScriptTypeRepository;
        $this->httpClientAdapter = $httpClientAdapter;
        $this->logger = $logger;
    }

    /**
     * Handle publish post request.
     *
     * @return ApiResponse
     */
    public function handlePublishAnalyticScriptRequest() : ApiResponse
    {
        $analyticScriptData = $this->requestStack->getCurrentRequest()->request
            ->get(RequestFieldsBag::PUBLISH_ANALYTIC_SCRIPT_DATA_FIELD);

        $apiResponse = new ApiResponse();

        if ($analyticScriptData !== null) {
            $event = new AnalyticScriptEvent($analyticScriptData);
            $this->eventDispatcher->dispatch(SystemEvents::PUBLISH_ANALYTIC_SCRIPT_EVENT, $event);

            if (!$event->isPublishedToQueue()) {
                $apiResponse
                    ->setContent(MessageBag::FAILED_TO_PUBLISH_ANALYTICS_SCRIPT_SHOWER_TO_QUEUE)
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            return $apiResponse;
        }

        return $apiResponse
            ->setContent(MessageBag::ANALYTIC_SCRIPT_NOT_RECEIVED)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Handle publish analytic script queue event.
     *
     * @param array $analyticScriptData
     * @return bool Is successful
     */
    public function handlePublishAnalyticScriptEvent(array $analyticScriptData) : bool
    {
        if (\is_array($analyticScriptData) && $analyticScriptData !== []) {
            try {
                $this->analyticsScriptTypeRepository->execPut(
                    $analyticScriptData,
                    ElasticSearchParametersBag::ID_ANALYTIC_SCRIPT
                );

                $event = new AnalyticScriptEvent($analyticScriptData);
                $this->eventDispatcher->dispatch(SystemEvents::PUBLISHED_ANALYTIC_SCRIPT_EVENT, $event);

                return true;
            } catch (\Throwable $e) {
                $this->logger->critical(MessageBag::FAILED_TO_ADD_ANALYTIC_SCRIPT_DATA_TO_INDEX, [
                    'error_message' => $e->getMessage(),
                    'data' => json_encode($analyticScriptData),
                ]);
            }
        } else {
            $this->logger->critical(MessageBag::ANALYTIC_SCRIPT_DATA_IS_BROKEN, [
                'queue' => SqsQueuesBag::PUBLISH_ANALYTIC_SCRIPT,
                'data' => $analyticScriptData,
            ]);
        }

        return false;
    }

    /**
     * Handle published analytic script queue event.
     *
     * @param array $analyticScriptData
     * @return bool Is successful
     */
    public function handlePublishedAnalyticScriptEvent(array $analyticScriptData) : bool
    {
        if (!isset($analyticScriptData['id'])) {
            $this->logger->critical(sprintf(MessageBag::ANALYTIC_SCRIPT_REQUIRED_FIELDS_MISSING, 'id'), [
                'queue' => SqsQueuesBag::PUBLISHED_ANALYTIC_SCRIPT,
                'data' => $analyticScriptData,
            ]);

            return false;
        }

        $dataToSend = [RequestFieldsBag::PUBLISHED_ANALYTIC_SCRIPT_ID_FIELD => $analyticScriptData['id']];

        try {
            $response = $this->httpClientAdapter->post($this->avalonAdminApiAnalyticScriptPublishedUri, $dataToSend);

            if ($response->getStatusCode() === Response::HTTP_OK) {
                $this->logger->info($response->getBody()->getContents());

                return true;
            }

            $this->logger->critical($response->getReasonPhrase(), [
                'status_code' => $response->getStatusCode(),
                'avalon_response' => $response->getBody()->getContents(),
            ]);
        } catch (\Throwable $e) {
            $this->logger->critical(MessageBag::FAILED_TO_SEND_ANALYTIC_SCRIPT_PUBLISHED_RESPONSE, [
                'error_message' => $e->getMessage(),
                'request_uri' => $this->avalonAdminApiAnalyticScriptPublishedUri,
                'request_data' => $dataToSend,
            ]);
        }

        return false;
    }
}

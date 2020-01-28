<?php

namespace App\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\{RequestStack, Response};
use App\Bags\{RequestFieldsBag, MessageBag, ElasticSearchParametersBag};
use App\Model\ApiResponse;
use App\Event\ConfigEvent;
use App\SystemEvents;
use App\Service\ElasticSearch\Repository\ConfigTypeRepository;

/**
 * Class ConfigService
 *
 * @package App\Service
 */
class ConfigService
{
    /**
     * @var string
     */
    private $publishedUrl;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ConfigTypeRepository
     */
    private $configTypeRepository;

    /**
     * @var HttpClientAdapter
     */
    private $httpClientAdapter;

    /**
     * ConfigService constructor.
     *
     * @param string $publishedUrl
     * @param RequestStack $requestStack
     * @param EventDispatcherInterface $eventDispatcher
     * @param ConfigTypeRepository $configTypeRepository
     * @param HttpClientAdapter $httpClientAdapter
     */
    public function __construct(
        string $publishedUrl,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        ConfigTypeRepository $configTypeRepository,
        HttpClientAdapter $httpClientAdapter
    )
    {
        $this->publishedUrl = $publishedUrl;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->configTypeRepository = $configTypeRepository;
        $this->httpClientAdapter = $httpClientAdapter;
    }

    /**
     * Handle publish config request.
     *
     * @return ApiResponse
     */
    public function handlePublishConfigRequest() : ApiResponse
    {
        $config = $this->getRequestStack()
            ->getCurrentRequest()
            ->request
            ->get(RequestFieldsBag::PUBLISH_CONFIG_DATA_FIELD);

        $response = new ApiResponse();

        $event = new ConfigEvent($config);
        $this->getEventDispatcher()->dispatch(
            SystemEvents::PUBLISH_CONFIG_EVENT, $event
        );

        if (!$event->isPublishedToQueue()) {
            $response
                ->setContent(MessageBag::FAILED_TO_PUBLISH_CONFIG)
                ->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    /**
     * Handle published config event.
     *
     * @param array $config
     *
     * @return bool
     */
    public function handlePublishConfigEvent(array $config) : bool
    {
        $this->getConfigTypeRepository()->execPut($config, $this->getConfigTypeId($config));

        $event = new ConfigEvent($config);
        $this->getEventDispatcher()->dispatch(
            SystemEvents::PUBLISHED_CONFIG_EVENT, $event
        );

        return true;
    }

    /**
     * Handle published config.
     *
     * @param array $config
     *
     * @return bool
     */
    public function handlePublishedConfigEvent(array $config) : bool
    {
        $requestBody = [
            RequestFieldsBag::PUBLISHED_CONFIG_ID_FIELD => $config['id']
        ];

        $response = $this->getHttpClientAdapter()
            ->post($this->getPublishedUrl(), $requestBody);

        if (Response::HTTP_OK === $response->getStatusCode()) {
            return true;
        }

        return false;
    }

    /**
     * Get publishedUrl.
     *
     * @return string
     */
    protected function getPublishedUrl() : string
    {
        return $this->publishedUrl;
    }

    /**
     * Get requestStack.
     *
     * @return RequestStack
     */
    protected function getRequestStack() : RequestStack
    {
        return $this->requestStack;
    }

    /**
     * Get eventDispatcher.
     *
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher() : EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * Get configTypeRepository.
     *
     * @return ConfigTypeRepository
     */
    protected function getConfigTypeRepository() : ConfigTypeRepository
    {
        return $this->configTypeRepository;
    }

    /**
     * Get httpClientAdapter.
     *
     * @return HttpClientAdapter
     */
    protected function getHttpClientAdapter() : HttpClientAdapter
    {
        return $this->httpClientAdapter;
    }

    /**
     * Get configTypeId.
     *
     * @param array $config
     *
     * @return string
     */
    protected function getConfigTypeId(array $config) : string
    {
        return ElasticSearchParametersBag::ID_CONFIG;
    }
}

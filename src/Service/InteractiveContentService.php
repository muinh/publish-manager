<?php

namespace App\Service;

use App\Bags\{InteractiveContentBag, MessageBag, RequestFieldsBag};
use App\Event\InteractiveContentEvent;
use App\Model\ApiResponse;
use App\Service\ElasticSearch\Repository\FlipCardTypeRepository;
use App\SystemEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\{RequestStack, Response};

/**
 * Class InteractiveContentService
 *
 * @package App\Service
 */
class InteractiveContentService
{
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
     * @var FlipCardTypeRepository
     */
    private $flipCardTypeRepository;

    /**
     * InteractiveContentService constructor.
     *
     * @codeCoverageIgnore
     *
     * @param RequestStack $requestStack
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param FlipCardTypeRepository $flipCardTypeRepository
     */
    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        FlipCardTypeRepository $flipCardTypeRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->flipCardTypeRepository = $flipCardTypeRepository;
    }

    /**
     * Handle publish request.
     *
     * @return ApiResponse
     */
    public function handlePublishRequest() : ApiResponse
    {
        $interactiveContentData = $this->requestStack->getCurrentRequest()->request
            ->get(RequestFieldsBag::PUBLISH_INTERACTIVE_CONTENT_DATA_FIELD);

        $apiResponse = new ApiResponse();

        if ($interactiveContentData !== null && \is_array($interactiveContentData)) {
            if ($this->isInteractiveContentValid($interactiveContentData) === false) {
                $message = MessageBag::INTERACTIVE_CONTENT_NOT_VALID;

                $this->logger->critical($message, [
                    'received_data' => $interactiveContentData,
                ]);

                return $apiResponse
                    ->setContent($message)
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            $event = new InteractiveContentEvent($interactiveContentData['type'], $interactiveContentData['data']);
            $this->eventDispatcher->dispatch(SystemEvents::PUBLISH_INTERACTIVE_CONTENT_EVENT, $event);

            return $apiResponse;
        }

        return $apiResponse
            ->setContent(MessageBag::INTERACTIVE_CONTENT_NOT_RECEIVED)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Handle publish event.
     *
     * @param InteractiveContentEvent $event
     * @return bool Is successful
     */
    public function handlePublishEvent(InteractiveContentEvent $event) : bool
    {
        $interactiveContentData = $event->getData();

        try {
            switch ($event->getType()) {
                case InteractiveContentBag::TYPE_FLIP_CARD:
                    $typeId = md5($interactiveContentData['id']);
                    $this->flipCardTypeRepository->execPut($event->getData(), $typeId);
                    break;
                default:
                    throw new \LogicException(
                        sprintf(MessageBag::INTERACTIVE_CONTENT_TYPE_NOT_HANDLED, $event->getType())
                    );
            }

            return true;
        } catch (\Throwable $e) {
            $this->logger->critical(MessageBag::FAILED_TO_ADD_INTERACTIVE_CONTENT_DATA_TO_INDEX, [
                'error_message' => $e->getMessage(),
                'post_data' => json_encode($interactiveContentData),
            ]);
        }

        return false;
    }

    /**
     * Check is interactive content valid.
     *
     * @param array $interactiveContentData
     * @return bool
     */
    private function isInteractiveContentValid(array $interactiveContentData) : bool
    {
        return isset($interactiveContentData['type'], $interactiveContentData['data'])
            && \in_array($interactiveContentData['type'], InteractiveContentBag::TYPES_LIST, true)
            && \is_array($interactiveContentData['data']);
    }
}

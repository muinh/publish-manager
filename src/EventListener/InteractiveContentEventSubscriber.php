<?php

namespace App\EventListener;

use App\Event\InteractiveContentEvent;
use App\Service\InteractiveContentService;
use App\SystemEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class InteractiveContentEventSubscriber
 *
 * @package App\EventListener
 */
class InteractiveContentEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            SystemEvents::PUBLISH_INTERACTIVE_CONTENT_EVENT => 'onPublishInteractiveContent'
        ];
    }

    /**
     * @var InteractiveContentService
     */
    private $interactiveContentService;

    /**
     * InteractiveContentEventSubscriber constructor.
     *
     * @codeCoverageIgnore
     *
     * @param InteractiveContentService $interactiveContentService
     */
    public function __construct(InteractiveContentService $interactiveContentService)
    {
        $this->interactiveContentService = $interactiveContentService;
    }

    /**
     * On publish seo tags event handler.
     *
     * @param InteractiveContentEvent $event
     * @return void
     */
    public function onPublishInteractiveContent(InteractiveContentEvent $event)
    {
        $this->interactiveContentService->handlePublishEvent($event);
    }
}

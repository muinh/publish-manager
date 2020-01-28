<?php

namespace App\EventListener;

use App\AwsSqs\QueuePublishers\SqsQueuePublisherInterface;
use App\Event\CategoryEvent;
use App\SystemEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CategoryEventSubscriber
 *
 * @package App\EventListener
 */
class CategoryEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            SystemEvents::PUBLISH_CATEGORY_EVENT => 'onPublishCategory',
            SystemEvents::PUBLISHED_CATEGORY_EVENT => 'onPublishedCategory'
        ];
    }

    /**
     * @var SqsQueuePublisherInterface
     */
    private $publisher;

    /**
     * CategoryEventSubscriber constructor.
     *
     * @codeCoverageIgnore
     *
     * @param SqsQueuePublisherInterface $publisher
     */
    public function __construct(SqsQueuePublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * On publish category event handler.
     *
     * @param CategoryEvent $event
     */
    public function onPublishCategory(CategoryEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publish($event->getCategory())
        );
    }

    /**
     * On published category event handler.
     *
     * @param CategoryEvent $event
     * @return void
     */
    public function onPublishedCategory(CategoryEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publishPublished($event->getCategory())
        );
    }
}

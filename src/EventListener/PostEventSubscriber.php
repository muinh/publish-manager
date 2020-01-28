<?php

namespace App\EventListener;

use App\AwsSqs\QueuePublishers\SqsQueuePublisherInterface;
use App\Event\PostEvent;
use App\SystemEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PublishPostSubscriber
 *
 * @package App\EventListener
 */
class PostEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            SystemEvents::PUBLISH_POST_EVENT => 'onPublishPost',
            SystemEvents::PUBLISHED_POST_EVENT => 'onPublishedPost'
        ];
    }

    /**
     * @var SqsQueuePublisherInterface
     */
    private $publisher;

    /**
     * PublishPostSubscriber constructor.
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
     * On publish post event handler.
     *
     * @param PostEvent $event
     * @return void
     */
    public function onPublishPost(PostEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publish($event->getPost())
        );
    }

    /**
     * On published post event handler.
     *
     * @param PostEvent $event
     * @return void
     */
    public function onPublishedPost(PostEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publishPublished($event->getPost())
        );
    }
}

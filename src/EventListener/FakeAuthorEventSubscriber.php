<?php

namespace App\EventListener;

use App\AwsSqs\QueuePublishers\SqsQueuePublisherInterface;
use App\Event\FakeAuthorEvent;
use App\SystemEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FakeAuthorEventSubscriber
 *
 * @package App\EventListener
 */
class FakeAuthorEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            SystemEvents::PUBLISH_FAKE_AUTHOR_EVENT => 'onPublishFakeAuthor',
            SystemEvents::PUBLISHED_FAKE_AUTHOR_EVENT => 'onPublishedFakeAuthor'
        ];
    }

    /**
     * @var SqsQueuePublisherInterface
     */
    private $publisher;

    /**
     * FakeAuthorEventSubscriber constructor.
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
     * On publish fake author event handler.
     *
     * @param FakeAuthorEvent $event
     */
    public function onPublishFakeAuthor(FakeAuthorEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publish($event->getFakeAuthor())
        );
    }

    /**
     * On published fake author event handler.
     *
     * @param FakeAuthorEvent $event
     */
    public function onPublishedFakeAuthor(FakeAuthorEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publishPublished($event->getFakeAuthor())
        );
    }
}
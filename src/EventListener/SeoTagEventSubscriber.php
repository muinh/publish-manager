<?php

namespace App\EventListener;

use App\AwsSqs\QueuePublishers\SqsQueuePublisherInterface;
use App\Event\SeoTagsEvent;
use App\SystemEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SeoTagEventSubscriber
 *
 * @package App\EventListener
 */
class SeoTagEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            SystemEvents::PUBLISH_SEO_TAGS_EVENT => 'onPublishSeoTags',
            SystemEvents::PUBLISHED_SEO_TAGS_EVENT => 'onPublishedSeoTags'
        ];
    }

    /**
     * @var SqsQueuePublisherInterface
     */
    private $publisher;

    /**
     * SeoTagEventSubscriber constructor.
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
     * On publish seo tags event handler.
     *
     * @param SeoTagsEvent $event
     * @return void
     */
    public function onPublishSeoTags(SeoTagsEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publish($event->getSeoTags())
        );
    }

    /**
     * On published seo tags event handler.
     *
     * @param SeoTagsEvent $event
     * @return void
     */
    public function onPublishedSeoTags(SeoTagsEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publishPublished($event->getSeoTags())
        );
    }
}

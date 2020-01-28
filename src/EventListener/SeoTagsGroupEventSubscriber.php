<?php

namespace App\EventListener;

use App\AwsSqs\QueuePublishers\SqsQueuePublisherInterface;
use App\Event\SeoTagsGroupsEvent;
use App\SystemEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SeoTagsGroupEventSubscriber
 *
 * @package App\EventListener
 */
class SeoTagsGroupEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            SystemEvents::PUBLISH_SEO_TAGS_GROUPS_EVENT => 'onPublishSeoTagsGroups',
            SystemEvents::PUBLISHED_SEO_TAGS_GROUPS_EVENT => 'onPublishedSeoTagsGroups'
        ];
    }

    /**
     * @var SqsQueuePublisherInterface
     */
    private $publisher;

    /**
     * SeoTagsGroupEventSubscriber constructor.
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
     * On publish seo tags groups event handler.
     *
     * @param SeoTagsGroupsEvent $event
     * @return void
     */
    public function onPublishSeoTagsGroups(SeoTagsGroupsEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publish($event->getSeoTagsGroups())
        );
    }

    /**
     * On published seo tags groups event handler.
     *
     * @param SeoTagsGroupsEvent $event
     * @return void
     */
    public function onPublishedSeoTagsGroups(SeoTagsGroupsEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publishPublished($event->getSeoTagsGroups())
        );
    }
}

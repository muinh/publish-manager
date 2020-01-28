<?php

namespace App\EventListener;

use App\AwsSqs\QueuePublishers\SqsQueuePublisherInterface;
use App\Event\AdsShowerEvent;
use App\SystemEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AdsShowerEventSubscriber
 *
 * @package App\EventListener
 */
class AdsShowerEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            SystemEvents::PUBLISH_ADS_SHOWER_EVENT => 'onPublishAdsShower',
            SystemEvents::PUBLISHED_ADS_SHOWER_EVENT => 'onPublishedAdsShower',
        ];
    }

    /**
     * @var SqsQueuePublisherInterface
     */
    private $publisher;

    /**
     * AdsShowerEventSubscriber constructor.
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
     * On publish ads shower event handler.
     *
     * @param AdsShowerEvent $event
     * @return void
     */
    public function onPublishAdsShower(AdsShowerEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publish($event->getAdsShowerData())
        );
    }

    /**
     * On published ads shower event handler.
     *
     * @param AdsShowerEvent $event
     * @return void
     */
    public function onPublishedAdsShower(AdsShowerEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publishPublished($event->getAdsShowerData())
        );
    }
}

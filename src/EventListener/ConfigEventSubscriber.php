<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\ConfigEvent;
use App\SystemEvents;
use App\AwsSqs\QueuePublishers\SqsQueuePublisherInterface;

/**
 * Class ConfigEventSubscriber
 *
 * @package App\EventListener
 */
class ConfigEventSubscriber implements EventSubscriberInterface
{
    /**
     * Undocumented variable
     *
     * @var SqsQueuePublisherInterface
     */
    private $sqsQueuePublisher;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            SystemEvents::PUBLISH_CONFIG_EVENT => 'onPublishConfig',
            SystemEvents::PUBLISHED_CONFIG_EVENT => 'onPublishedConfig',
        ];
    }

    /**
     * ConfigEventSubscriber constructor.
     *
     * @param SqsQueuePublisherInterface $sqsQueuePublisher
     */
    public function __construct(SqsQueuePublisherInterface $sqsQueuePublisher)
    {
        $this->sqsQueuePublisher = $sqsQueuePublisher;
    }

    /**
     * Handle publish config event.
     *
     * @param ConfigEvent $configEvent
     */
    public function onPublishConfig(ConfigEvent $configEvent)
    {
        $configEvent->setPublishedToQueue(
            $this->getSqsQueuePublisher()->publish($configEvent->getConfig())
        );
    }

    /**
     * Handle published config event.
     *
     * @param ConfigEvent $configEvent
     */
    public function onPublishedConfig(ConfigEvent $configEvent)
    {
        $configEvent->setPublishedToQueue(
            $this->getSqsQueuePublisher()->publishPublished($configEvent->getConfig())
        );
    }

    /**
     * Get sqsEueuePublisher.
     *
     * @return SqsQueuePublisherInterface
     */
    protected function getSqsQueuePublisher() : SqsQueuePublisherInterface
    {
        return $this->sqsQueuePublisher;
    }
}

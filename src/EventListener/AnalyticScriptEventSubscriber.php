<?php

namespace App\EventListener;

use App\AwsSqs\QueuePublishers\SqsQueuePublisherInterface;
use App\Event\AnalyticScriptEvent;
use App\SystemEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AnalyticScriptEventSubscriber
 *
 * @package App\EventListener
 */
class AnalyticScriptEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            SystemEvents::PUBLISH_ANALYTIC_SCRIPT_EVENT => 'onPublishAnalyticScript',
            SystemEvents::PUBLISHED_ANALYTIC_SCRIPT_EVENT => 'onPublishedAnalyticScript',
        ];
    }

    /**
     * @var SqsQueuePublisherInterface
     */
    private $publisher;

    /**
     * AnalyticScriptEventSubscriber constructor.
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
     * On publish analytic script event handler.
     *
     * @param AnalyticScriptEvent $event
     * @return void
     */
    public function onPublishAnalyticScript(AnalyticScriptEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publish($event->getAnalyticScriptData())
        );
    }

    /**
     * On published analytic script event handler.
     *
     * @param AnalyticScriptEvent $event
     * @return void
     */
    public function onPublishedAnalyticScript(AnalyticScriptEvent $event)
    {
        $event->setPublishedToQueue(
            $this->publisher->publishPublished($event->getAnalyticScriptData())
        );
    }
}

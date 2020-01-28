<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class AnalyticScriptEvent
 *
 * @package App\Event
 */
class AnalyticScriptEvent extends Event
{
    /**
     * @var array
     */
    private $analyticScriptData;

    /**
     * @var bool
     */
    private $publishedToQueue;

    /**
     * AnalyticScriptEvent constructor.
     *
     * @param array $analyticScriptData
     */
    public function __construct(array $analyticScriptData)
    {
        $this->analyticScriptData = $analyticScriptData;
        $this->publishedToQueue = false;
    }

    /**
     * Get analyticScriptData.
     *
     * @return array
     */
    public function getAnalyticScriptData() : array
    {
        return $this->analyticScriptData;
    }

    /**
     * Get publishedToQueue.
     *
     * @return bool
     */
    public function isPublishedToQueue() : bool
    {
        return $this->publishedToQueue;
    }

    /**
     * Set publishedToQueue.
     *
     * @param bool $publishedToQueue
     * @return AnalyticScriptEvent
     */
    public function setPublishedToQueue(bool $publishedToQueue) : AnalyticScriptEvent
    {
        $this->publishedToQueue = $publishedToQueue;

        return $this;
    }
}

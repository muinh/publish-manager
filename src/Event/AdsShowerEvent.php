<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class AdsShowerEvent
 *
 * @package App\Event
 */
class AdsShowerEvent extends Event
{
    /**
     * @var array
     */
    private $adsShowerData;

    /**
     * @var bool
     */
    private $publishedToQueue;

    /**
     * AdsShowerEvent constructor.
     *
     * @param array $adsShowerData
     */
    public function __construct(array $adsShowerData)
    {
        $this->adsShowerData = $adsShowerData;
        $this->publishedToQueue = false;
    }

    /**
     * Get adsShowerData.
     *
     * @return array
     */
    public function getAdsShowerData() : array
    {
        return $this->adsShowerData;
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
     * @return AdsShowerEvent
     */
    public function setPublishedToQueue(bool $publishedToQueue) : AdsShowerEvent
    {
        $this->publishedToQueue = $publishedToQueue;

        return $this;
    }
}

<?php

namespace App\Traits;

/**
 * Class IsPublishedToQueueTrait
 *
 * @package IsPublishedToQueueTrait
 */
trait IsPublishedToQueueTrait
{
    /**
     * @var bool
     */
    private $publishedToQueue = false;

    /**
     * Is published to queue.
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
     */
    public function setPublishedToQueue(bool $publishedToQueue)
    {
        $this->publishedToQueue = $publishedToQueue;

        return $this;
    }
}

<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class FakeAuthorEvent
 *
 * @package App\Event
 */
class FakeAuthorEvent extends Event
{
    /**
     * @var array
     */
    private $fakeAuthor;

    /**
     * @var bool
     */
    private $publishedToQueue;

    /**
     * FakeAuthorEvent constructor.
     *
     * @param array $fakeAuthor
     */
    public function __construct(array $fakeAuthor)
    {
        $this->fakeAuthor = $fakeAuthor;
        $this->publishedToQueue = false;
    }

    /**
     * Get fake author.
     *
     * @return array
     */
    public function getFakeAuthor() : array
    {
        return $this->fakeAuthor;
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
     * @return FakeAuthorEvent
     */
    public function setPublishedToQueue(bool $publishedToQueue) : FakeAuthorEvent
    {
        $this->publishedToQueue = $publishedToQueue;

        return $this;
    }
}
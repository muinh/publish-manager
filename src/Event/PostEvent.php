<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class PostEvent
 *
 * @package App\Event
 */
class PostEvent extends Event
{
    /**
     * @var array
     */
    private $post;

    /**
     * @var bool
     */
    private $publishedToQueue;

    /**
     * PostEvent constructor.
     *
     * @param array $post
     */
    public function __construct(array $post)
    {
        $this->post = $post;
        $this->publishedToQueue = false;
    }

    /**
     * Get post.
     *
     * @return array
     */
    public function getPost() : array
    {
        return $this->post;
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
     * @return PostEvent
     */
    public function setPublishedToQueue(bool $publishedToQueue) : PostEvent
    {
        $this->publishedToQueue = $publishedToQueue;

        return $this;
    }
}

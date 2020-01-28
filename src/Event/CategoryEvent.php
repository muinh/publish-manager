<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class CategoryEvent
 *
 * @package App\Event
 */
class CategoryEvent extends Event
{
    /**
     * @var array
     */
    private $category;

    /**
     * @var bool
     */
    private $publishedToQueue;

    /**
     * CategoryEvent constructor.
     *
     * @param array $category
     */
    public function __construct(array $category)
    {
        $this->category = $category;
        $this->publishedToQueue = false;
    }

    /**
     * Get category.
     *
     * @return array
     */
    public function getCategory() : array
    {
        return $this->category;
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
     * @return CategoryEvent
     */
    public function setPublishedToQueue(bool $publishedToQueue) : CategoryEvent
    {
        $this->publishedToQueue = $publishedToQueue;

        return $this;
    }
}

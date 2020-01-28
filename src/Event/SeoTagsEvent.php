<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class SeoTagsEvent
 *
 * @package App\Event
 */
class SeoTagsEvent extends Event
{
    /**
     * All seoTags for project.
     *
     * @var array
     */
    private $seoTags;

    /**
     * @var bool
     */
    private $publishedToQueue;

    /**
     * SeoTagsEvent constructor.
     *
     * @param array $seoTags
     */
    public function __construct(array $seoTags)
    {
        $this->seoTags = $seoTags;
        $this->publishedToQueue = false;
    }

    /**
     * Get seoTags.
     *
     * @return array
     */
    public function getSeoTags() : array
    {
        return $this->seoTags;
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
     * @return SeoTagsEvent
     */
    public function setPublishedToQueue(bool $publishedToQueue) : SeoTagsEvent
    {
        $this->publishedToQueue = $publishedToQueue;

        return $this;
    }
}

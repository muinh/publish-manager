<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class SeoTagsGroupsEvent
 *
 * @package App\Event
 */
class SeoTagsGroupsEvent extends Event
{
    /**
     * All seoTagsGroups for project.
     *
     * @var array
     */
    private $seoTagsGroups;

    /**
     * @var bool
     */
    private $publishedToQueue;

    /**
     * SeoTagsGroupsEvent constructor.
     *
     * @param array $seoTagsGroups
     */
    public function __construct(array $seoTagsGroups)
    {
        $this->seoTagsGroups = $seoTagsGroups;
        $this->publishedToQueue = false;
    }

    /**
     * Get seoTagsGroups.
     *
     * @return array
     */
    public function getSeoTagsGroups() : array
    {
        return $this->seoTagsGroups;
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
     * @return SeoTagsGroupsEvent
     */
    public function setPublishedToQueue(bool $publishedToQueue) : SeoTagsGroupsEvent
    {
        $this->publishedToQueue = $publishedToQueue;

        return $this;
    }
}

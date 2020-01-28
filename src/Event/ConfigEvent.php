<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Traits\IsPublishedToQueueTrait;

/**
 * Class ConfigEvent
 *
 * @package App\Event
 */
class ConfigEvent extends Event
{
    use IsPublishedToQueueTrait;

    /**
     * @var array
     */
    private $config;

    /**
     * ConfigEvent constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get config.
     *
     * @return array
     */
    public function getConfig() : array
    {
        return $this->config;
    }
}
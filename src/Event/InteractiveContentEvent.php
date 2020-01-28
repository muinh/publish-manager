<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class InteractiveContentEvent
 *
 * @package App\Event
 */
class InteractiveContentEvent extends Event
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $data;

    /**
     * InteractiveContentEvent constructor.
     *
     * @param string $type
     * @param array $data
     */
    public function __construct(string $type, array $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }
}

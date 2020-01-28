<?php

namespace App\Service\Logger\Processor;

/**
 * Class TagProcessor
 *
 * @package App\Service\Logger\Processor
 */
class TagProcessor
{
    /**
     * Tag name.
     *
     * @var string $tag
     */
    private $tag;

    /**
     * TagProcessor constructor.
     *
     * @param string $tag
     */
    public function __construct(string $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Processes record.
     *
     * @param array $record
     * @return array
     */
    public function __invoke(array $record) : array
    {
        $record['extra']['tag'] = $this->tag;

        return $record;
    }
}

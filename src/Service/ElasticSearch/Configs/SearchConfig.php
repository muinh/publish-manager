<?php

namespace App\Service\ElasticSearch\Configs;

/**
 * Class SearchConfig
 *
 * @codeCoverageIgnore
 *
 * @package App\Service\ElasticSearch\Configs
 */
class SearchConfig
{
    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var array
     */
    private $sortParams;

    /**
     * @var array
     */
    private $fields;

    /**
     * SearchConfig constructor.
     */
    public function __construct()
    {
        $this->fields = [];
    }

    /**
     * Get offset
     *
     * @return int|null
     */
    public function getOffset() : ?int
    {
        return $this->offset;
    }

    /**
     * Set offset
     *
     * @param int $offset
     * @return SearchConfig
     */
    public function setOffset(int $offset) : SearchConfig
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Get limit
     *
     * @return int|null
     */
    public function getLimit() : ?int
    {
        return $this->limit;
    }

    /**
     * Set limit
     *
     * @param int $limit
     * @return SearchConfig
     */
    public function setLimit(int $limit) : SearchConfig
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get sortParams
     *
     * @return array|null
     */
    public function getSortParams() : ?array
    {
        return $this->sortParams;
    }

    /**
     * Set sortParams
     *
     * @param array $sortParams
     * @return SearchConfig
     */
    public function setSortParams(array $sortParams) : SearchConfig
    {
        $this->sortParams = $sortParams;

        return $this;
    }

    /**
     * Get fields.
     *
     * @return array
     */
    public function getFields() : array
    {
        return $this->fields;
    }

    /**
     * Set fields.
     *
     * @param array $fields
     * @return SearchConfig
     */
    public function setFields(array $fields) : SearchConfig
    {
        $this->fields = $fields;

        return $this;
    }
}
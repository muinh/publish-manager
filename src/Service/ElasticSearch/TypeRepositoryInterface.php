<?php

namespace App\Service\ElasticSearch;

/**
 * Interface TypeRepositoryInterface
 *
 * @package App\Service\ElasticSearch
 */
interface TypeRepositoryInterface
{
    /**
     * Get elasticsearch index type.
     *
     * @return string
     */
    public function getType() : string;

    /**
     * Delete from type by id.
     *
     * @param string $id
     * @return array
     */
    public function execDeleteByTypeId(string $id) : array;
}

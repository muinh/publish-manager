<?php

namespace App\Service\ElasticSearch;

use Elasticsearch\Client;

/**
 * Interface ElasticSearchClientFactoryInterface
 *
 * @package App\Service\ElasticSearch
 */
interface ElasticSearchClientFactoryInterface
{
    /**
     * Get elastic search client from config.
     *
     * @param array $configs
     * @return Client
     */
    public function getClient(array $configs = []) : Client;
}

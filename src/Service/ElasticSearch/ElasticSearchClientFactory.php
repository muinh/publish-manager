<?php

namespace App\Service\ElasticSearch;

use Elasticsearch\{Client, ClientBuilder};
use Elasticsearch\Common\Exceptions\RuntimeException;

/**
 * Class ElasticSearchClientFactory
 *
 * @package App\Service\ElasticSearch
 */
class ElasticSearchClientFactory implements ElasticSearchClientFactoryInterface
{
    /**
     * @var array
     */
    private $defaultConfigs;

    /**
     * ElasticSearchClientFactory constructor.
     *
     * @codeCoverageIgnore
     *
     * @param array $defaultConfigs
     */
    public function __construct(array $defaultConfigs = [])
    {
        $this->defaultConfigs = $defaultConfigs;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function getClient(array $configs = []) : Client
    {
        $this->defaultConfigs = array_merge($this->defaultConfigs, $configs);

        return ClientBuilder::fromConfig($this->defaultConfigs, true);
    }
}

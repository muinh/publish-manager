<?php

namespace App\Service\ElasticSearch;

use App\Bags\ElasticSearchParametersBag;
use Elasticsearch\Client;

/**
 * Class IndexService
 *
 * @package App\Service\ElasticSearch
 */
class IndexService
{
    /**
     * @var string
     */
    private $projectIndex;

    /**
     * @var ElasticSearchClientFactoryInterface
     */
    private $elasticSearchClientFactory;

    /**
     * In memory storage for client instance.
     *
     * @var Client
     */
    private $client;

    /**
     * IndexService constructor.
     *
     * @param string $projectIndex
     * @param ElasticSearchClientFactoryInterface $elasticSearchClientFactory
     */
    public function __construct(
        string $projectIndex,
        ElasticSearchClientFactoryInterface $elasticSearchClientFactory
    ) {
        $this->projectIndex = $projectIndex;
        $this->elasticSearchClientFactory = $elasticSearchClientFactory;
    }

    /**
     * Backup index.
     *
     * @param string|null $backupIndexName
     * @return array
     */
    public function backupIndex(string $backupIndexName = null) : array
    {
        return $this->reIndex($this->projectIndex, $backupIndexName ?? $this->projectIndex . '_old');
    }

    /**
     * Restore index.
     *
     * @param string|null $backupIndexName
     * @return array
     */
    public function restoreIndex(string $backupIndexName = null) : array
    {
        return $this->reIndex($backupIndexName ?? $this->projectIndex . '_old', $this->projectIndex);
    }

    /**
     * Create index with mapping.
     *
     * @param string|null $newIndexName
     * @return array
     */
    public function createIndex(string $newIndexName = null)
    {
        $params = [
            'index' => $newIndexName ?? $this->projectIndex,
            'body' => [
                'mappings' => [
                    ElasticSearchParametersBag::TYPE_POST => [
                        'properties' => [
                            'url' => [
                                'type' => 'string',
                                'index' => 'not_analyzed'
                            ]
                        ]
                    ],
                    ElasticSearchParametersBag::TYPE_CATEGORY => [
                        'properties' => [
                            'url' => [
                                'type' => 'string',
                                'index' => 'not_analyzed'
                            ]
                        ]
                    ],
                    ElasticSearchParametersBag::TYPE_SEO_TAG => [
                        'properties' => [
                            'url' => [
                                'type' => 'string',
                                'index' => 'not_analyzed'
                            ]
                        ]
                    ],
                    ElasticSearchParametersBag::TYPE_SEO_TAGS_GROUP => [
                        'properties' => [
                            'url' => [
                                'type' => 'string',
                                'index' => 'not_analyzed'
                            ]
                        ]
                    ],
                    ElasticSearchParametersBag::TYPE_FAKE_AUTHOR => [
                        'properties' => [
                            'url' => [
                                'type' => 'string',
                                'index' => 'not_analyzed'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->getClient()->indices()->create($params);
    }

    /**
     * Delete index.
     *
     * @param string|null $indexToDelete
     * @return array
     */
    public function deleteIndex(string $indexToDelete = null)
    {
        $params = [
            'index' => $indexToDelete ?? $this->projectIndex
        ];

        return $this->getClient()->indices()->delete($params);
    }

    /**
     * Reindex data from index to index.
     *
     * @param string $fromIndex
     * @param string $toIndex
     * @return array
     */
    private function reIndex(string $fromIndex, string $toIndex) : array
    {
        $params = [
            'body' => [
                'conflicts' => 'proceed',
                'source' => [
                    'index' => $fromIndex,
                ],
                'dest' => [
                    'index' => $toIndex,
                    'version_type' => 'external'
                ]
            ]
        ];

        return $this->getClient()->reindex($params);
    }

    /**
     * Get client instance.
     *
     * @return Client
     */
    private function getClient() : Client
    {
        if ($this->client === null) {
            $this->client = $this->elasticSearchClientFactory->getClient();
        }

        return $this->client;
    }
}

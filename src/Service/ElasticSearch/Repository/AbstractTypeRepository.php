<?php

namespace App\Service\ElasticSearch\Repository;

use App\Service\ElasticSearch\Configs\SearchConfig;
use App\Service\ElasticSearch\{ElasticSearchClientFactoryInterface, TypeRepositoryInterface};
use Elasticsearch\Client;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractTypeRepository
 *
 * @package App\Service\ElasticSearch\Repository
 */
abstract class AbstractTypeRepository implements TypeRepositoryInterface
{
    /**
     * Error message on Search request.
     */
    private const SEARCH_REQUEST_FAILED = 'Search request to elastic search by params [%s] failed with message: [%s]';

    /**
     * Error message on Get request.
     */
    private const GET_REQUEST_FAILED = 'Get request to elastic search by params [%s] failed with message: [%s]';

    /**
     * Error message on Update request.
     */
    private const UPDATE_REQUEST_FAILED = 'Update request to elastic search by params [%s] failed with message: [%s]';

    /**
     * Error message on Update By Query request.
     */
    private const UPDATE_REQUEST_BY_QUERY_FAILED = 'Update by Query request to elastic search by params [%s] failed with message: [%s]';

    /**
     * Error message on Delete request.
     */
    private const DELETE_REQUEST_FAILED = 'Delete request to elastic search by params [%s] failed with message: [%s]';

    /**
     * @var string
     */
    private $projectIndex;

    /**
     * @var ElasticSearchClientFactoryInterface
     */
    private $elasticSearchClientFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * In memory storage for client instance.
     *
     * @var Client
     */
    private $client;

    /**
     * AbstractTypeRepository constructor.
     *
     * @codeCoverageIgnore
     *
     * @param string $projectIndex
     * @param ElasticSearchClientFactoryInterface $elasticSearchClientFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $projectIndex,
        ElasticSearchClientFactoryInterface $elasticSearchClientFactory,
        LoggerInterface $logger
    ) {
        $this->projectIndex = $projectIndex;
        $this->elasticSearchClientFactory = $elasticSearchClientFactory;
        $this->logger = $logger;
    }

    /**
     * Get elastic index project type to use.
     *
     * @return string
     */
    abstract public function getType() : string;

    /**
     * Execute search request.
     *
     * @param array $body
     * @param array $customParams
     * @return array
     */
    public function execSearch(array $body, array $customParams = []) : array
    {
        $params = $this->getParams($customParams);
        $params['body'] = $body;

        try {
            return $this->getClient()->search($params);
        } catch (\Throwable $e) {
            $this->logger->critical(sprintf(self::SEARCH_REQUEST_FAILED, json_encode($params), $e->getMessage()));
        }

        return [];
    }

    /**
     * Execute search with specific body of request and configurations from search config.
     *
     * @param array $body
     * @param SearchConfig $searchConfig
     * @return array
     */
    protected function execSearchByConfig(array $body, SearchConfig $searchConfig) : array
    {
        $params = [
            'from' => $searchConfig->getOffset() ?? 0,
            'request_cache' => true,
        ];

        if ($searchConfig->getLimit() !== null) {
            $params['size'] = $searchConfig->getLimit();
        }

        if ($searchConfig->getSortParams() !== null) {
            $params['sort'] = $searchConfig->getSortParams();
        }

        return $this->execSearch($body, $params);
    }

    /**
     * Execute get request.
     *
     * @param string|null $id
     * @param array $customParams
     * @return array
     */
    protected function execGet(?string $id = null, array $customParams = []) : array
    {
        $params = $this->getParams($customParams);
        $params['id'] = $id;

        try {
            return $this->getClient()->get($params);
        } catch (\Throwable $e) {
            $this->logger->critical(sprintf(self::GET_REQUEST_FAILED, json_encode($params), $e->getMessage()));
        }

        return [];
    }

    /**
     * Execute put request.
     *
     * @param $body
     * @param string $id|null
     * @return array
     */
    public function execPut($body, string $id = null) : array
    {
        $params = $this->getParams(['body' => $body]);

        if ($id !== null) {
            $params['id'] = $id;
        }

        return $this->getClient()->index($params);
    }

    /**
     * Execute update request.
     *
     * @param array $customParams
     * @return array
     */
    public function execUpdate(array $customParams = []) : array
    {
        $params = $this->getParams($customParams);

        try {
            return $this->getClient()->update($params);
        } catch (\Throwable $e) {
            $this->logger->critical(sprintf(self::UPDATE_REQUEST_FAILED, json_encode($params), $e->getMessage()));
        }

        return [];
    }

    /**
     * Execute update request by query.
     *
     * @param array $customParams
     * @return int
     */
    public function execUpdateByQuery(array $customParams = []) : int
    {
        $params = $this->getParams($customParams);

        try {
            $result = $this->getClient()->updateByQuery($params);

            return $result['updated'] ?? 0;
        } catch (\Throwable $e) {
            $this->logger->critical(sprintf(self::UPDATE_REQUEST_BY_QUERY_FAILED, json_encode($params), $e->getMessage()));

            return 0;
        }
    }

    /**
     * Get all by params.
     *
     * @return array
     */
    public function getAll() : array
    {
        try {
            $params = $this->getParams();
            $searchResult = $this->getClient()->search($params);

            $hits = $this->getClient()->extractArgument($searchResult, 'hits');

            if ($hits['total'] > 0) {
                return array_column($this->getClient()->extractArgument($hits, 'hits'), '_source');
            }
        } catch (\Throwable $exception) {
            return [];
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function execDeleteByTypeId(string $id) : array
    {
        $params = $this->getParams(['id' => $id]);

        try {
            return $this->getClient()->delete($params);
        } catch (\Throwable $exception) {
            $this->logger->critical(sprintf(self::DELETE_REQUEST_FAILED, json_encode($params), $exception->getMessage()));

            return [];
        }
    }

    /**
     * Delete by query.
     *
     * @param array $body
     * @return array
     */
    protected function deleteByQuery(array $body) : array
    {
        $params = $this->getParams(['body' => $body]);

        return $this->getClient()->deleteByQuery($params);
    }

    /**
     * Get parameters for request to elastic search.
     *
     * @param array $customParams
     * @return array
     */
    private function getParams(array $customParams = []) : array
    {
        return array_merge($this->getBaseParams(), $customParams);
    }

    /**
     * Get base parameters.
     *
     * @return array
     */
    private function getBaseParams() : array
    {
        return [
            'index' => $this->getIndex(),
            'type' => $this->getType(),
        ];
    }

    /**
     * Get project index.
     *
     * @return string
     */
    protected function getIndex() : string
    {
        return $this->projectIndex;
    }

    /**
     * Get client instance.
     *
     * @return Client
     */
    protected function getClient() : Client
    {
        if ($this->client === null) {
            $this->client = $this->elasticSearchClientFactory->getClient();
        }

        return $this->client;
    }
}
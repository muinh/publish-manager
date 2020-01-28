<?php

namespace Tests\App\Service\ElasticSearch;

use App\Service\ElasticSearch\{ElasticSearchClientFactoryInterface, ElasticSearchIndexManager};
use Elasticsearch\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ElasticSearchIndexManagerTest
 *
 * @package Tests\App\Service\ElasticSearch
 */
abstract class ElasticSearchIndexManagerTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $elasticSearchClientFactoryInterfaceMock;

    /**
     * @var string
     */
    protected $projectIndex;
    
    /**
     * @var ElasticSearchIndexManager
     */
    protected $elasticSearchIndexManager;
    
    /**
     * @var MockObject
     */
    protected $clientMock;

    /**
     * @var array
     */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->elasticSearchClientFactoryInterfaceMock = $this->createMock(ElasticSearchClientFactoryInterface::class);
        $this->projectIndex = 'test';
        $this->clientMock = $this->createMock(Client::class);

        if ($this->mockMethods) {
            $this->elasticSearchIndexManager = $this->getMockBuilder(ElasticSearchIndexManager::class)
                ->setConstructorArgs([$this->elasticSearchClientFactoryInterfaceMock, $this->projectIndex])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->elasticSearchIndexManager = new ElasticSearchIndexManager(
                $this->elasticSearchClientFactoryInterfaceMock, $this->projectIndex
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->elasticSearchIndexManager = null;
        gc_collect_cycles();
    }
}
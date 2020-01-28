<?php

namespace Tests\App\Service\ElasticSearch;

use App\Service\ElasticSearch\ElasticSearchClientFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class ElasticSearchClientFactoryTest
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 *
 * @package Tests\App\Service\ElasticSearch
 */
abstract class ElasticSearchClientFactoryTest extends TestCase
{
    /** @var ElasticSearchClientFactory */
    protected $elasticSearchClientFactory;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if ($this->mockMethods) {
            $this->elasticSearchClientFactory = $this->getMockBuilder(ElasticSearchClientFactory::class)
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->elasticSearchClientFactory = new ElasticSearchClientFactory();
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        \Mockery::close();
        $this->elasticSearchClientFactory = null;
        gc_collect_cycles();
    }
}
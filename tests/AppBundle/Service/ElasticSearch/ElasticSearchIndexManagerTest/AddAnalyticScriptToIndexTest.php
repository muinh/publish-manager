<?php

namespace Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;

use Mockery\Exception\RuntimeException;
use Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;
use Tests\App\Stub\ElasticSearchConstantBag;

/**
 * Class AddAnalyticScriptToIndexTest
 *
 * @package Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest
 */
class AddAnalyticScriptToIndexTest extends ElasticSearchIndexManagerTest
{
    /**
     * @var array
     */
    protected $mockMethods = ['getClient'];

    /**
     * Test method on success with creating client.
     *
     * @covers \App\Service\ElasticSearch\ElasticSearchIndexManager::addAnalyticScriptToIndex()
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $testAnalyticScriptData = [
            'analytic_script' => '<script></script>',
        ];

        $params = [
            'index' => $this->projectIndex,
            'type' => ElasticSearchConstantBag::TYPE_ANALYTIC_SCRIPT,
            'id' => ElasticSearchConstantBag::ID_ANALYTIC_SCRIPT,
            'body' => $testAnalyticScriptData,
        ];
    
        // Mocking
        $this->elasticSearchIndexManager
            ->expects($this->once())
            ->method('getClient')
            ->willReturn($this->clientMock);
    
        $this->clientMock
            ->expects($this->once())
            ->method('index')
            ->with($params)
            ->willReturn($testAnalyticScriptData);
        
        //Execution
        $methodResult = $this->elasticSearchIndexManager->addAnalyticScriptToIndex($testAnalyticScriptData);
    
        // Asserting
        $this->assertEquals($testAnalyticScriptData, $methodResult);
    }
}
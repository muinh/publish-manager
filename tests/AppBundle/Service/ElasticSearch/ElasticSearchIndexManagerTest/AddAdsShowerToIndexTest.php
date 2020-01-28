<?php

namespace Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;

use Mockery\Exception\RuntimeException;
use Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;
use Tests\App\Stub\ElasticSearchConstantBag;

/**
 * Class AddAdsShowerToIndexTest
 *
 * @package Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest
 */
class AddAdsShowerToIndexTest extends ElasticSearchIndexManagerTest
{
    /**
     * @var array
     */
    protected $mockMethods = ['getClient'];

    /**
     * Test method on success with creating client.
     *
     * @covers \App\Service\ElasticSearch\ElasticSearchIndexManager::addAdsShowerToIndex()
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $testAdsShowerData = [
            'ads_shower_name' => 'test name',
        ];

        $params = [
            'index' => $this->projectIndex,
            'type' => ElasticSearchConstantBag::TYPE_ADS_SHOWER,
            'id' => ElasticSearchConstantBag::ID_ADS_SHOWER,
            'body' => $testAdsShowerData,
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
            ->willReturn($testAdsShowerData);
        
        //Execution
        $methodResult = $this->elasticSearchIndexManager->addAdsShowerToIndex($testAdsShowerData);
    
        // Asserting
        $this->assertEquals($testAdsShowerData, $methodResult);
    }
}
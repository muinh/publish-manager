<?php

namespace Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;

use Mockery\Exception\RuntimeException;
use Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;
use Tests\App\Stub\ElasticSearchConstantBag;

/**
 * Class DeleteOldCategoryFromIndexTest
 *
 * @package Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest
 */
class DeleteOldCategoryFromIndexTest extends ElasticSearchIndexManagerTest
{
    /**
     * @var array
     */
    protected $mockMethods = ['getClient'];

    /**
     * Test method on success with creating client.
     *
     * @covers \App\Service\ElasticSearch\ElasticSearchIndexManager::deleteOldCategoryFromIndex()
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $testCategoryUrlHash = 'test-id';
        $params = [
            'index' => $this->projectIndex,
            'type' => ElasticSearchConstantBag::TYPE_CATEGORY,
            'id' => $testCategoryUrlHash,
        ];
        $testReturnArray = [];
    
        // Mocking
        $this->elasticSearchIndexManager
            ->expects($this->once())
            ->method('getClient')
            ->willReturn($this->clientMock);
    
        $this->clientMock
            ->expects($this->once())
            ->method('delete')
            ->with($params)
            ->willReturn($testReturnArray);
        
        //Execution
        $methodResult = $this->elasticSearchIndexManager->deleteOldCategoryFromIndex($testCategoryUrlHash);
        
        // Asserting
        $this->assertEquals($testReturnArray, $methodResult);
    }
}
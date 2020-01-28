<?php

namespace Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;

use Mockery\Exception\RuntimeException;
use Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;
use Tests\App\Stub\ElasticSearchConstantBag;

/**
 * Class AddCategoryToIndexTest
 *
 * @package Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest
 */
class AddCategoryToIndexTest extends ElasticSearchIndexManagerTest
{
    /**
     * @var array
     */
    protected $mockMethods = ['getClient'];

    /**
     * Test method on success with creating client.
     *
     * @covers \App\Service\ElasticSearch\ElasticSearchIndexManager::addCategoryToIndex()
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $testCategoryUrlHash = 'test-id';
        $testCategoryData = [
            'category_name' => 'test name',
            'category_content' => 'test content'
        ];
        $params = [
            'index' => $this->projectIndex,
            'type' => ElasticSearchConstantBag::TYPE_CATEGORY,
            'id' => $testCategoryUrlHash,
            'body' => $testCategoryData,
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
            ->willReturn($testCategoryData);
        
        //Execution
        $methodResult = $this->elasticSearchIndexManager->addCategoryToIndex($testCategoryUrlHash, $testCategoryData);
    
        // Asserting
        $this->assertEquals($testCategoryData, $methodResult);
    }
}
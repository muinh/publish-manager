<?php

namespace Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;

use Elasticsearch\Common\Exceptions\Missing404Exception;
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;
use Tests\App\Stub\ElasticSearchConstantBag;

/**
 * Class GetAllCategoriesTest
 *
 * @package Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest
 */
class GetAllCategoriesTest extends ElasticSearchIndexManagerTest
{
    /**
     * @var array
     */
    protected $mockMethods = ['getClient'];
    
    /**
     * Success test with not zero total categories.
     *
     * @covers \App\Service\ElasticSearch\ElasticSearchIndexManager::getAllCategories()
     * @throws RuntimeException
     */
    public function testSuccessWithNotZeroTotalCategories()
    {
        // Test data
        $params = [
            'index' => $this->projectIndex,
            'type' => ElasticSearchConstantBag::TYPE_CATEGORY
        ];
    
        $categoriesArray = [
            'category_name' => 'test name',
            'category_id' => 'test id'
        ];
        $categoriesHitsArray = [
            ['_source' => $categoriesArray]
        ];
        $categoriesHitsHitsArray = [
            'total' => 12,
            'hits' => $categoriesHitsArray
        ];
        $categoriesResponseArray = [
            'hits' => $categoriesHitsHitsArray
        ];
    
        // Mocking
        $this->elasticSearchIndexManager
            ->expects($this->any())
            ->method('getClient')
            ->willReturn($this->clientMock);
    
        $this->clientMock->expects($this->once())
            ->method('search')
            ->with($this->equalTo($params))
            ->willReturn($categoriesResponseArray);
    
        $this->clientMock->expects($this->at(1))
            ->method('extractArgument')
            ->with($this->equalTo($categoriesResponseArray), $this->equalTo('hits'))
            ->willReturn($categoriesHitsHitsArray);
    
        $this->clientMock->expects($this->at(2))
            ->method('extractArgument')
            ->with($this->equalTo($categoriesHitsHitsArray), $this->equalTo('hits'))
            ->willReturn($categoriesHitsArray);

        // Execution
        $methodResult = $this->elasticSearchIndexManager->getAllCategories();
    
        // Asserting
        $this->assertEquals([$categoriesArray], $methodResult);
    }
    
    /**
     * Success test with zero total categories.
     *
     * @covers \App\Service\ElasticSearch\ElasticSearchIndexManager::getAllCategories()
     * @throws RuntimeException
     */
    public function testSuccessWithZeroTotalCategories()
    {
        // Test data
        $params = [
            'index' => $this->projectIndex,
            'type' => ElasticSearchConstantBag::TYPE_CATEGORY
        ];
        
        $categoriesArray = [];
        $categoriesHitsArray = [
            ['_source' => $categoriesArray]
        ];
        $categoriesHitsHitsArray = [
            'total' => 0,
            'hits' => $categoriesHitsArray
        ];
        $categoriesResponseArray = [
            'hits' => $categoriesHitsHitsArray
        ];
        
        // Mocking
        $this->elasticSearchIndexManager
            ->expects($this->any())
            ->method('getClient')
            ->willReturn($this->clientMock);
        
        $this->clientMock
            ->expects($this->once())
            ->method('search')
            ->with($this->equalTo($params))
            ->willReturn($categoriesResponseArray);
        
        $this->clientMock
            ->expects($this->at(1))
            ->method('extractArgument')
            ->with($this->equalTo($categoriesResponseArray), $this->equalTo('hits'))
            ->willReturn($categoriesHitsHitsArray);
        
        // Execution
        $methodResult = $this->elasticSearchIndexManager->getAllCategories();
        
        // Asserting
        $this->assertEquals($categoriesArray, $methodResult);
    }
    
    /**
     * Failure test.
     *
     * @covers \App\Service\ElasticSearch\ElasticSearchIndexManager::getAllCategories()
     * @throws RuntimeException
     */
    public function testFailure()
    {
        // Test data
        $params = [
            'index' => $this->projectIndex,
            'type' => ElasticSearchConstantBag::TYPE_CATEGORY
        ];
        $expectedResult = [];
    
        // Mocking
        $this->elasticSearchIndexManager
            ->expects($this->any())
            ->method('getClient')
            ->willReturn($this->clientMock);
    
        $this->clientMock->expects($this->once())
            ->method('search')
            ->with($this->equalTo($params))
            ->willThrowException(new Missing404Exception());
    
        // Execution
        $methodResult = $this->elasticSearchIndexManager->getAllCategories();
    
        // Asserting
        $this->assertEquals($expectedResult, $methodResult);
    }
}
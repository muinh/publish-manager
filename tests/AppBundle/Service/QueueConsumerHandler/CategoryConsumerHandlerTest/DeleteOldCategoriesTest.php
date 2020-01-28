<?php

namespace Tests\App\Service\QueueConsumerHandler\CategoryConsumerHandlerTest;

use Mockery\Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Service\QueueConsumerHandler\CategoryConsumerHandlerTest;
use Tests\PrivateMethodInvocationTrait;

/**
 * Class DeleteOldCategoriesTest
 *
 * @package Tests\App\Service\QueueConsumerHandler\CategoryConsumerHandlerTest
 */
class DeleteOldCategoriesTest extends CategoryConsumerHandlerTest
{
    use PrivateMethodInvocationTrait;
    
    /**
     * Test success.
     *
     * @covers \App\Service\QueueConsumerHandler\CategoryConsumerHandler::handleOldCategories()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $testSearchCategoryData = [
            'id' => 1,
            'url' => 1,
            'categories_data' => 'test data'
        ];
        $testSearchCategoriesData = [$testSearchCategoryData];
    
        $testNewCategoryData = [
            'id' => 2,
            'url' => 2,
            'categories_data' => 'test data'
        ];
        $testNewCategoriesData = [$testNewCategoryData];
        
        // Mocking
        $this->elasticSearchIndexManagerMock
            ->expects($this->once())
            ->method('getAllCategories')
            ->willReturn($testSearchCategoriesData);
    
        $this->elasticSearchIndexManagerMock
            ->expects($this->once())
            ->method('deleteOldCategoryFromIndex')
            ->with($this->equalTo(md5($testSearchCategoryData['url'])));
        
        // Execution
        $this->invokeMethod($this->categoryConsumerHandler, 'deleteOldCategories', $testNewCategoriesData);
    }
    
    /**
     * Test success without deleting.
     *
     * @covers \App\Service\QueueConsumerHandler\CategoryConsumerHandler::handleOldCategories()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testSuccessWithoutDeleting()
    {
        // Test data
        $testSearchCategoryData = [
            'id' => 1,
            'url' => 1,
            'categories_data' => 'test data'
        ];
        $testSearchCategoriesData = [$testSearchCategoryData];
        
        $testNewCategoryData = [
            'id' => 1,
            'url' => 1,
            'categories_data' => 'test data'
        ];
        $testNewCategoriesData = [$testNewCategoryData];
        
        // Mocking
        $this->elasticSearchIndexManagerMock
            ->expects($this->once())
            ->method('getAllCategories')
            ->willReturn($testSearchCategoriesData);
        
        // Execution
        $this->invokeMethod($this->categoryConsumerHandler, 'deleteOldCategories', $testNewCategoriesData);
    }
}
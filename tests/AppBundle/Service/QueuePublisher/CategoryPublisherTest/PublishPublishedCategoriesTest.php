<?php

namespace Tests\App\Service\QueuePublisher\CategoryPublisherTest;

use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Service\QueuePublisher\CategoryPublisherTest;

/**
 * Class PublishPublishedCategoriesTest
 *
 * @package Tests\App\Service\QueuePublisher\CategoryPublisherTest
 */
class PublishPublishedCategoriesTest extends CategoryPublisherTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Service\QueuePublisher\CategoryPublisher::publishPublishedCategories()
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $testCategoriesData = [
            'category_id' => 'test-id',
            'category_name' => 'test-name',
        ];
        $testFormat = 'json';
        $testSerializedData = json_encode($testCategoriesData);
        
        // Mocking
        $this->serializerInterfaceMock
            ->expects($this->once())
            ->method('serialize')
            ->with($this->equalTo($testCategoriesData), $this->equalTo($testFormat))
            ->willReturn($testSerializedData);
        
        $this->publishedCategoryProducerInterfaceMock
            ->expects($this->once())
            ->method('publish')
            ->with($this->equalTo($testSerializedData));
        
        //Execution
        $this->categoryPublisher->publishPublishedCategories($testCategoriesData);
    }
}
<?php

namespace Tests\App\Service\QueuePublisher\PostPublisherTest;

use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Service\QueuePublisher\PostPublisherTest;

/**
 * Class PublishPostTest
 *
 * @package Tests\App\Service\QueuePublisher\PostPublisherTest
 */
class PublishPostTest extends PostPublisherTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Service\QueuePublisher\PostPublisher::publishPost()
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $testPostData = [
            'post_id' => 'test-id',
            'post_name' => 'test-name',
        ];
        $testFormat = 'json';
        $testSerializedData = json_encode($testPostData);
        
        // Mocking
        $this->serializerInterfaceMock
            ->expects($this->once())
            ->method('serialize')
            ->with($this->equalTo($testPostData), $this->equalTo($testFormat))
            ->willReturn($testSerializedData);
        
        $this->publishPostProducerInterfaceMock
            ->expects($this->once())
            ->method('publish')
            ->with($this->equalTo($testSerializedData));
        
        //Execution
        $this->postPublisher->publishPost($testPostData);
    }
}
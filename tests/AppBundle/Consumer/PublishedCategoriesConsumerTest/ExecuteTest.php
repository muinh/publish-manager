<?php

namespace Tests\App\Consumer\PublishedCategoriesConsumerTest;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Consumer\PublishedCategoriesConsumerTest;

/**
 * Class ExecuteTest
 *
 * @package Tests\App\Consumer\PublishedCategoriesConsumerTest
 */
class ExecuteTest extends PublishedCategoriesConsumerTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Consumer\PublishedCategoriesConsumer::execute()
     * @throws Exception
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        
        // Mocking
        $this->categoryConsumerHandler
            ->expects($this->once())
            ->method('handlePublishedCategories')
            ->with($this->equalTo($amqpMessage));
        
        //Execution
        $this->publishedCategoriesConsumer->execute($amqpMessage);
    }
}
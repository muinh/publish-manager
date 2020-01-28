<?php

namespace Tests\App\Consumer\PublishCategoriesConsumerTest;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Consumer\PublishCategoriesConsumerTest;

/**
 * Class ExecuteTest
 *
 * @package Tests\App\Consumer\PublishCategoriesConsumerTest
 */
class ExecuteTest extends PublishCategoriesConsumerTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Consumer\PublishCategoriesConsumer::execute()
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
            ->method('handlePublishCategories')
            ->with($this->equalTo($amqpMessage));
        
        //Execution
        $this->publishCategoriesConsumer->execute($amqpMessage);
    }
}
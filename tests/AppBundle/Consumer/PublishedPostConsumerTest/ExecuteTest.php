<?php

namespace Tests\App\Consumer\PublishedPostConsumerTest;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Consumer\PublishedPostConsumerTest;

/**
 * Class ExecuteTest
 *
 * @package Tests\App\Consumer\PublishedPostConsumerTest
 */
class ExecuteTest extends PublishedPostConsumerTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Consumer\PublishedPostConsumer::execute()
     * @throws Exception
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        
        // Mocking
        $this->postConsumerHandler
            ->expects($this->once())
            ->method('handlePublishedPost')
            ->with($this->equalTo($amqpMessage));
        
        //Execution
        $this->publishedPostConsumer->execute($amqpMessage);
    }
}
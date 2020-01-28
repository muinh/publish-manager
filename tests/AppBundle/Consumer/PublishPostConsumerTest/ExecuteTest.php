<?php

namespace Tests\App\Consumer\PublishPostConsumerTest;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Consumer\PublishPostConsumerTest;

/**
 * Class ExecuteTest
 *
 * @package Tests\App\Consumer\PublishPostConsumerTest
 */
class ExecuteTest extends PublishPostConsumerTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Consumer\PublishPostConsumer::execute()
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
            ->method('handlePublishPost')
            ->with($this->equalTo($amqpMessage));
        
        //Execution
        $this->publishPostConsumer->execute($amqpMessage);
    }
}
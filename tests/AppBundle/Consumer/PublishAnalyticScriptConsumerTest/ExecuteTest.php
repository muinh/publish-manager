<?php

namespace Tests\App\Consumer\PublishAnalyticScriptConsumerTest;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Consumer\PublishAnalyticScriptConsumerTest;

/**
 * Class ExecuteTest
 *
 * @package Tests\App\Consumer\PublishAnalyticScriptConsumerTest
 */
class ExecuteTest extends PublishAnalyticScriptConsumerTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Consumer\PublishAnalyticScriptConsumer::execute()
     * @throws Exception
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        
        // Mocking
        $this->analyticScriptConsumerHandler
            ->expects($this->once())
            ->method('handlePublishAnalyticScript')
            ->with($this->equalTo($amqpMessage));
        
        //Execution
        $this->publishAnalyticScriptConsumer->execute($amqpMessage);
    }
}
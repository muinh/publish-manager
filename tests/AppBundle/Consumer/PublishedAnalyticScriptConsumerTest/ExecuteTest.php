<?php

namespace Tests\App\Consumer\PublishedAnalyticScriptConsumerTest;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Consumer\PublishedAnalyticScriptConsumerTest;

/**
 * Class ExecuteTest
 *
 * @package Tests\App\Consumer\PublishedAnalyticScriptConsumerTest
 */
class ExecuteTest extends PublishedAnalyticScriptConsumerTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Consumer\PublishedAnalyticScriptConsumer::execute()
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
            ->method('handlePublishedAnalyticScript')
            ->with($this->equalTo($amqpMessage));
        
        //Execution
        $this->publishedAnalyticScriptConsumer->execute($amqpMessage);
    }
}
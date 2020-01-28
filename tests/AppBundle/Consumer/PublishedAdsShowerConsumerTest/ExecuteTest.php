<?php

namespace Tests\App\Consumer\PublishedAdsShowerConsumerTest;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Consumer\PublishedAdsShowerConsumerTest;

/**
 * Class ExecuteTest
 *
 * @package Tests\App\Consumer\PublishedAdsShowerConsumerTest
 */
class ExecuteTest extends PublishedAdsShowerConsumerTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Consumer\PublishedAdsShowerConsumer::execute()
     * @throws Exception
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        
        // Mocking
        $this->adsShowerConsumerHandler
            ->expects($this->once())
            ->method('handlePublishedAdsShower')
            ->with($this->equalTo($amqpMessage));
        
        //Execution
        $this->publishedAdsShowerConsumer->execute($amqpMessage);
    }
}
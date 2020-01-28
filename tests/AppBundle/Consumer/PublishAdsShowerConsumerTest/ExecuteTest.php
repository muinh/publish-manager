<?php

namespace Tests\App\Consumer\PublishAdsShowerConsumerTest;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Consumer\PublishAdsShowerConsumerTest;

/**
 * Class ExecuteTest
 *
 * @package Tests\App\Consumer\PublishAdsShowerConsumerTest
 */
class ExecuteTest extends PublishAdsShowerConsumerTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Consumer\PublishAdsShowerConsumer::execute()
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
            ->method('handlePublishAdsShower')
            ->with($this->equalTo($amqpMessage));
        
        //Execution
        $this->publishAdsShowerConsumer->execute($amqpMessage);
    }
}
<?php

namespace Tests\App\Service\QueueConsumerHandler\AdsShowerConsumerHandlerTest;

use App\Event\AdsShowerEvent;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\{AssertionFailedError, Exception};
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Service\QueueConsumerHandler\AdsShowerConsumerHandlerTest;
use Tests\App\Stub\{MessageBag, SystemEvents};

/**
 * Class HandlePublishAdsShowerTest
 *
 * @package Tests\App\Service\QueueConsumerHandler\AdsShowerConsumerHandlerTest
 */
class HandlePublishAdsShowerTest extends AdsShowerConsumerHandlerTest
{
    /**
     * Test success.
     *
     * @covers \App\Service\QueueConsumerHandler\AdsShowerConsumerHandler::handlePublishAdsShower()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testAdsShowerData = ['ads_shower' => 'test data'];
        $testEvent = new AdsShowerEvent($testAdsShowerData);
        $testEncodedAdsShowerData = json_encode($testAdsShowerData);
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testEncodedAdsShowerData);
        
        $this->elasticSearchIndexManagerMock
            ->expects($this->once())
            ->method('addAdsShowerToIndex')
            ->with($this->equalTo($testAdsShowerData));
        
        $this->eventDispatcherInterfaceMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(SystemEvents::PUBLISHED_ADS_SHOWER_EVENT), $this->equalTo($testEvent));
    
        // Execution
        $methodResult = $this->adsShowerConsumerHandler->handlePublishAdsShower($amqpMessage);
    
        // Asserts
        $this->assertTrue($methodResult);
    }
    
    /**
     * Test failure.
     *
     * @covers \App\Service\QueueConsumerHandler\AdsShowerConsumerHandler::handlePublishAdsShower()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testFailure()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testAdsShowerData = ['ads_shower' => 'test data'];
        $testEncodedAdsShowerData = json_encode($testAdsShowerData);
        $testException = new \Exception();
        $testErrorsData = [
            'error_message' => $testException->getMessage(),
            'ads_shower_data' => $testEncodedAdsShowerData,
        ];
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testEncodedAdsShowerData);
        
        $this->elasticSearchIndexManagerMock
            ->expects($this->once())
            ->method('addAdsShowerToIndex')
            ->willThrowException($testException);
    
        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(MessageBag::FAILED_TO_ADD_ADS_SHOWER_DATA_TO_INDEX),
                $this->equalTo($testErrorsData)
            );
        
        // Execution
        $methodResult = $this->adsShowerConsumerHandler->handlePublishAdsShower($amqpMessage);
        
        // Asserts
        $this->assertFalse($methodResult);
    }
    
    /**
     * Test failure with broken data.
     *
     * @covers \App\Service\QueueConsumerHandler\AdsShowerConsumerHandler::handlePublishAdsShower()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testFailureWithBrokenData()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testAdsShowerData = null;
        $testParamsForLog = [
            'consumer' => 'publish_ads_shower',
            'message_body' => $testAdsShowerData,
        ];
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testAdsShowerData);

        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(MessageBag::ADS_SHOWER_DATA_IS_BROKEN),
                $this->equalTo($testParamsForLog)
            );
        
        // Execution
        $methodResult = $this->adsShowerConsumerHandler->handlePublishAdsShower($amqpMessage);
        
        // Asserts
        $this->assertFalse($methodResult);
    }
}
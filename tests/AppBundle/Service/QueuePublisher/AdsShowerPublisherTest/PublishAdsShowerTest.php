<?php

namespace Tests\App\Service\QueuePublisher\AdsShowerPublisherTest;

use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Service\QueuePublisher\AdsShowerPublisherTest;

/**
 * Class PublishAdsShowerTest
 *
 * @package Tests\App\Service\QueuePublisher\AdsShowerPublisherTest
 */
class PublishAdsShowerTest extends AdsShowerPublisherTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Service\QueuePublisher\AdsShowerPublisher::publishAdsShower()
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $testAdsShowerData = [
            'id' => 'test-id',
            'content' => 'test-content',
        ];
        $testFormat = 'json';
        $testSerializedData = json_encode($testAdsShowerData);
    
        // Mocking
        $this->serializerInterfaceMock
            ->expects($this->once())
            ->method('serialize')
            ->with($this->equalTo($testAdsShowerData), $this->equalTo($testFormat))
            ->willReturn($testSerializedData);

        $this->publishAdsShowerProducerInterfaceMock
            ->expects($this->once())
            ->method('publish')
            ->with($this->equalTo($testSerializedData));
    
        //Execution
        $this->adsShowerPublisher->publishAdsShower($testAdsShowerData);
    }
}
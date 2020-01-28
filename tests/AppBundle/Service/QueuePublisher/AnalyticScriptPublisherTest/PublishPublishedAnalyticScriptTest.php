<?php

namespace Tests\App\Service\QueuePublisher\AnalyticScriptPublisherTest;

use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Service\QueuePublisher\AnalyticScriptPublisherTest;

/**
 * Class PublishPublishedAnalyticScriptTest
 *
 * @package Tests\App\Service\QueuePublisher\AnalyticScriptPublisherTest
 */
class PublishPublishedAnalyticScriptTest extends AnalyticScriptPublisherTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Service\QueuePublisher\AnalyticScriptPublisher::publishPublishedAnalyticScript()
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $testAnalyticScriptData = [
            'id' => 'test-id',
            'content' => 'test-content',
        ];
        $testFormat = 'json';
        $testSerializedData = json_encode($testAnalyticScriptData);
        
        // Mocking
        $this->serializerInterfaceMock
            ->expects($this->once())
            ->method('serialize')
            ->with($this->equalTo($testAnalyticScriptData), $this->equalTo($testFormat))
            ->willReturn($testSerializedData);
        
        $this->publishedAnalyticScriptProducerInterfaceMock
            ->expects($this->once())
            ->method('publish')
            ->with($this->equalTo($testSerializedData));
        
        //Execution
        $this->analyticScriptPublisher->publishPublishedAnalyticScript($testAnalyticScriptData);
    }
}
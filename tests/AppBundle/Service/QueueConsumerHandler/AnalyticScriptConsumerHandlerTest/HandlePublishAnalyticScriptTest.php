<?php

namespace Tests\App\Service\QueueConsumerHandler\AnalyticScriptConsumerHandlerTest;

use App\Event\AnalyticScriptEvent;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\{AssertionFailedError, Exception};
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Service\QueueConsumerHandler\AnalyticScriptConsumerHandlerTest;
use Tests\App\Stub\{MessageBag, SystemEvents};

/**
 * Class HandlePublishAnalyticScriptTest
 *
 * @package Tests\App\Service\QueueConsumerHandler\AnalyticScriptConsumerHandlerTest
 */
class HandlePublishAnalyticScriptTest extends AnalyticScriptConsumerHandlerTest
{
    /**
     * Test success.
     *
     * @covers \App\Service\QueueConsumerHandler\AnalyticScriptConsumerHandler::handlePublishAnalyticScript()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testAnalyticScriptData = ['analytic_script' => 'test data'];
        $testEvent = new AnalyticScriptEvent($testAnalyticScriptData);
        $testEncodedAnalyticScriptData = json_encode($testAnalyticScriptData);
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testEncodedAnalyticScriptData);
        
        $this->elasticSearchIndexManagerMock
            ->expects($this->once())
            ->method('addAnalyticScriptToIndex')
            ->with($this->equalTo($testAnalyticScriptData));
        
        $this->eventDispatcherInterfaceMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(SystemEvents::PUBLISHED_ANALYTIC_SCRIPT_EVENT), $this->equalTo($testEvent));
    
        // Execution
        $methodResult = $this->analyticScriptConsumerHandler->handlePublishAnalyticScript($amqpMessage);
    
        // Asserts
        $this->assertTrue($methodResult);
    }
    
    /**
     * Test failure.
     *
     * @covers \App\Service\QueueConsumerHandler\AnalyticScriptConsumerHandler::handlePublishAnalyticScript()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testFailure()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testAnalyticScriptData = ['analytic_script' => 'test data'];
        $testEncodedAnalyticScriptData = json_encode($testAnalyticScriptData);
        $testException = new \Exception();
        $testErrorsData = [
            'error_message' => $testException->getMessage(),
            'analytics_script_data' => $testEncodedAnalyticScriptData,
        ];
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testEncodedAnalyticScriptData);
        
        $this->elasticSearchIndexManagerMock
            ->expects($this->once())
            ->method('addAnalyticScriptToIndex')
            ->willThrowException($testException);
    
        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(MessageBag::FAILED_TO_ADD_ANALYTIC_SCRIPT_DATA_TO_INDEX),
                $this->equalTo($testErrorsData)
            );
        
        // Execution
        $methodResult = $this->analyticScriptConsumerHandler->handlePublishAnalyticScript($amqpMessage);
        
        // Asserts
        $this->assertFalse($methodResult);
    }
    
    /**
     * Test failure with broken data.
     *
     * @covers \App\Service\QueueConsumerHandler\AnalyticScriptConsumerHandler::handlePublishAnalyticScript()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testFailureWithBrokenData()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testAnalyticScriptData = null;
        $testParamsForLog = [
            'consumer' => 'publish_analytic_script',
            'message_body' => $testAnalyticScriptData,
        ];
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testAnalyticScriptData);

        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(MessageBag::ANALYTIC_SCRIPT_DATA_IS_BROKEN),
                $this->equalTo($testParamsForLog)
            );
        
        // Execution
        $methodResult = $this->analyticScriptConsumerHandler->handlePublishAnalyticScript($amqpMessage);
        
        // Asserts
        $this->assertFalse($methodResult);
    }
}
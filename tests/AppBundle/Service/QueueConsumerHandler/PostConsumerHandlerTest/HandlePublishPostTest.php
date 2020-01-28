<?php

namespace Tests\App\Service\QueueConsumerHandler\PostConsumerHandlerTest;

use App\Event\PostEvent;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\{AssertionFailedError, Exception};
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Service\QueueConsumerHandler\PostConsumerHandlerTest;
use Tests\App\Stub\{MessageBag, SystemEvents};

/**
 * Class HandlePublishPostTest
 *
 * @package Tests\App\Service\QueueConsumerHandler\PostConsumerHandlerTest
 */
class HandlePublishPostTest extends PostConsumerHandlerTest
{
    /**
     * Test success.
     *
     * @covers \App\Service\QueueConsumerHandler\PostConsumerHandler::handlePublishPost()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testPostUrl = 'test url';
        $testPostUrlHash = md5($testPostUrl);
        $testPostData = [
            'url' => $testPostUrl,
            'post_data' => 'test data'
        ];
        $testEvent = new PostEvent($testPostData);
        $testEncodedPostData = json_encode($testPostData);
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testEncodedPostData);
        
        $this->elasticSearchIndexManagerMock
            ->expects($this->once())
            ->method('addPostToIndex')
            ->with($this->equalTo($testPostUrlHash), $this->equalTo($testPostData));
        
        $this->eventDispatcherInterfaceMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(SystemEvents::PUBLISHED_POST_EVENT), $this->equalTo($testEvent));
    
        // Execution
        $methodResult = $this->postConsumerHandler->handlePublishPost($amqpMessage);
    
        // Asserts
        $this->assertTrue($methodResult);
    }

    /**
     * Test failure.
     *
     * @covers \App\Service\QueueConsumerHandler\PostConsumerHandler::handlePublishPost()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testFailure()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testPostData = [
            'url' => 'test url',
            'post_data' => 'test data'
        ];
        $testEncodedPostData = json_encode($testPostData);
        $testException = new \Exception();
        $testErrorsData = [
            'error_message' => $testException->getMessage(),
            'post_data' => $testEncodedPostData,
        ];
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testEncodedPostData);
        
        $this->elasticSearchIndexManagerMock
            ->expects($this->once())
            ->method('addPostToIndex')
            ->willThrowException($testException);
    
        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(MessageBag::FAILED_TO_ADD_POST_DATA_TO_INDEX),
                $this->equalTo($testErrorsData)
            );
        
        // Execution
        $methodResult = $this->postConsumerHandler->handlePublishPost($amqpMessage);
        
        // Asserts
        $this->assertFalse($methodResult);
    }
    
    /**
     * Test failure with broken data.
     *
     * @covers \App\Service\QueueConsumerHandler\PostConsumerHandler::handlePublishPost()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testFailureWithBrokenData()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testPostData = null;
        $testParamsForLog = [
            'consumer' => 'publish_post',
            'message_body' => $testPostData,
        ];
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testPostData);

        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(MessageBag::POST_DATA_IS_BROKEN),
                $this->equalTo($testParamsForLog)
            );
        
        // Execution
        $methodResult = $this->postConsumerHandler->handlePublishPost($amqpMessage);
        
        // Asserts
        $this->assertFalse($methodResult);
    }
}
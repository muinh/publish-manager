<?php

namespace Tests\App\Service\QueueConsumerHandler\CategoryConsumerHandlerTest;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\{AssertionFailedError, Exception};
use PHPUnit\Framework\MockObject\RuntimeException;
use Psr\Http\Message\{ResponseInterface, StreamInterface};
use Symfony\Component\HttpFoundation\Response;
use Tests\App\Service\QueueConsumerHandler\CategoryConsumerHandlerTest;
use Tests\App\Stub\{MessageBag, RequestFieldsBag};

/**
 * Class HandlePublishedCategoryTest
 *
 * @package Tests\App\Service\QueueConsumerHandler\CategoryConsumerHandlerTest
 */
class HandlePublishedCategoryTest extends CategoryConsumerHandlerTest
{
    /**
     * Test success.
     *
     * @covers \App\Service\QueueConsumerHandler\CategoryConsumerHandler::handlePublishedCategories()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testCategoryData = [
            'id' => 1,
            'categories_data' => 'test data'
        ];
        $testCategoriesData = [$testCategoryData];
        $testDataToSend = [RequestFieldsBag::PUBLISHED_CATEGORIES_IDS_FIELD => [$testCategoryData['id']]];
        $responseInterfaceMock = $this->createMock(ResponseInterface::class);
        $streamInterfaceMock = $this->createMock(StreamInterface::class);
        $testLogContent = 'test log info';
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(json_encode($testCategoriesData));
        
        $this->httpClientAdapterMock
            ->expects($this->once())
            ->method('post')
            ->with($this->equalTo($this->testCmsAdminApiCategoryPublishedUri), $this->equalTo($testDataToSend))
            ->willReturn($responseInterfaceMock);
    
        $responseInterfaceMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);
    
        $responseInterfaceMock
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($streamInterfaceMock);
        
        $streamInterfaceMock
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($testLogContent);
        
        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('info')
            ->with($this->equalTo($testLogContent));
    
        // Execution
        $methodResult = $this->categoryConsumerHandler->handlePublishedCategories($amqpMessage);
    
        // Asserts
        $this->assertTrue($methodResult);
    }
    
    /**
     * Test failure on send http request.
     *
     * @covers \App\Service\QueueConsumerHandler\CategoryConsumerHandler::handlePublishedCategories()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testFailureOnSend()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testCategoryData = [
            'id' => 1,
            'categories_data' => 'test data'
        ];
        $testCategoriesData = [$testCategoryData];
        $testDataToSend = [RequestFieldsBag::PUBLISHED_CATEGORIES_IDS_FIELD => [$testCategoryData['id']]];
        $responseInterfaceMock = $this->createMock(ResponseInterface::class);
        $streamInterfaceMock = $this->createMock(StreamInterface::class);
    
        $testReasonPhrase = 'test reason phrase';
        $testLogMessage = 'test log message';
        $testStatusCode = Response::HTTP_BAD_REQUEST;
        $testLogContent = [
            'status_code' => $testStatusCode,
            'response_body' => $testLogMessage,
        ];
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(json_encode($testCategoriesData));
        
        $this->httpClientAdapterMock
            ->expects($this->once())
            ->method('post')
            ->with($this->equalTo($this->testCmsAdminApiCategoryPublishedUri), $this->equalTo($testDataToSend))
            ->willReturn($responseInterfaceMock);
        
        $responseInterfaceMock
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($testStatusCode);
        
        $responseInterfaceMock
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($streamInterfaceMock);
        
        $streamInterfaceMock
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($testLogMessage);
    
        $responseInterfaceMock
            ->expects($this->once())
            ->method('getReasonPhrase')
            ->willReturn($testReasonPhrase);
        
        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('critical')
            ->with($this->equalTo($testReasonPhrase), $this->equalTo($testLogContent));
        
        // Execution
        $methodResult = $this->categoryConsumerHandler->handlePublishedCategories($amqpMessage);
        
        // Asserts
        $this->assertFalse($methodResult);
    }
    
    /**
     * Test failure with exception.
     *
     * @covers \App\Service\QueueConsumerHandler\CategoryConsumerHandler::handlePublishedCategories()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testFailureWithException()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testCategoryData = [
            'id' => 1,
            'categories_data' => 'test data'
        ];
        $testCategoriesData = [$testCategoryData];
        $testDataToSend = [RequestFieldsBag::PUBLISHED_CATEGORIES_IDS_FIELD => [$testCategoryData['id']]];
        
        $testException = new Exception();
        $testLogContent = [
            'error_message' => $testException->getMessage(),
            'request_uri' => $this->testCmsAdminApiCategoryPublishedUri,
            'request_data' => $testDataToSend,
        ];
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(json_encode($testCategoriesData));
        
        $this->httpClientAdapterMock
            ->expects($this->once())
            ->method('post')
            ->with($this->equalTo($this->testCmsAdminApiCategoryPublishedUri), $this->equalTo($testDataToSend))
            ->willThrowException($testException);
        
        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(MessageBag::FAILED_TO_SEND_CATEGORIES_PUBLISHED_RESPONSE),
                $this->equalTo($testLogContent)
            );
        
        // Execution
        $methodResult = $this->categoryConsumerHandler->handlePublishedCategories($amqpMessage);
        
        // Asserts
        $this->assertFalse($methodResult);
    }
    
    /**
     * Test failure if data is broken.
     *
     * @covers \App\Service\QueueConsumerHandler\CategoryConsumerHandler::handlePublishedCategories()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testFailureIfDataIsBroken()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testCategoryData = [
            'categories_data' => 'test data'
        ];
        $testCategoriesData = [$testCategoryData];
        
        $testLogContent = [
            'categories_data' => $testCategoriesData,
            'consumer' => 'published_category_consumer',
        ];
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(json_encode($testCategoriesData));

        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(sprintf(MessageBag::CATEGORY_REQUIRED_FIELDS_MISSING, 'id')),
                $this->equalTo($testLogContent)
            );
        
        // Execution
        $methodResult = $this->categoryConsumerHandler->handlePublishedCategories($amqpMessage);
        
        // Asserts
        $this->assertFalse($methodResult);
    }
}
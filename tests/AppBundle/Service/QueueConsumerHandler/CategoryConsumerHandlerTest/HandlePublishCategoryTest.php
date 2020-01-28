<?php

namespace Tests\App\Service\QueueConsumerHandler\CategoryConsumerHandlerTest;

use App\Event\CategoryEvent;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\{AssertionFailedError, Exception};
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Service\QueueConsumerHandler\CategoryConsumerHandlerTest;
use Tests\App\Stub\{MessageBag, SystemEvents};

/**
 * Class HandlePublishCategoryTest
 *
 * @package Tests\App\Service\QueueConsumerHandler\CategoryConsumerHandlerTest
 */
class HandlePublishCategoryTest extends CategoryConsumerHandlerTest
{
    /**
     * @var array
     */
    protected $mockMethods = ['deleteOldCategories'];

    /**
     * Test success.
     *
     * @covers \App\Service\QueueConsumerHandler\CategoryConsumerHandler::handlePublishCategories()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testCategoryUrlHash = md5('test url');
        $testCategoryData = ['url' => 'test url'];
        $testCategoriesData = [$testCategoryData];
        $testEvent = new CategoryEvent($testCategoriesData);
        $testEncodedCategoriesData = json_encode($testCategoriesData);
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testEncodedCategoriesData);
        
        $this->elasticSearchIndexManagerMock
            ->expects($this->once())
            ->method('addCategoryToIndex')
            ->with($this->equalTo($testCategoryUrlHash), $this->equalTo($testCategoryData));
        
        $this->categoryConsumerHandler
            ->expects($this->once())
            ->method('deleteOldCategories');
        
        $this->eventDispatcherInterfaceMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(SystemEvents::PUBLISHED_CATEGORIES_EVENT), $this->equalTo($testEvent));
    
        // Execution
        $methodResult = $this->categoryConsumerHandler->handlePublishCategories($amqpMessage);
    
        // Asserts
        $this->assertTrue($methodResult);
    }
    
    /**
     * Test failure.
     *
     * @covers \App\Service\QueueConsumerHandler\CategoryConsumerHandler::handlePublishCategories()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testFailure()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testCategoryData = ['url' => 'test url'];
        $testCategoriesData = [$testCategoryData];
        $testEncodedCategoriesData = json_encode($testCategoriesData);
        $testException = new \Exception();
        $testErrorsData = [
            'error_message' => $testException->getMessage(),
            'categories_data' => $testEncodedCategoriesData,
        ];
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testEncodedCategoriesData);
        
        $this->elasticSearchIndexManagerMock
            ->expects($this->once())
            ->method('addCategoryToIndex')
            ->willThrowException($testException);
    
        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(MessageBag::FAILED_TO_ADD_CATEGORIES_DATA_TO_INDEX),
                $this->equalTo($testErrorsData)
            );
        
        // Execution
        $methodResult = $this->categoryConsumerHandler->handlePublishCategories($amqpMessage);
        
        // Asserts
        $this->assertFalse($methodResult);
    }
    
    /**
     * Test failure with broken data.
     *
     * @covers \App\Service\QueueConsumerHandler\CategoryConsumerHandler::handlePublishCategories()
     *
     * @throws AssertionFailedError
     * @throws Exception
     * @throws RuntimeException
     */
    public function testFailureWithBrokenData()
    {
        // Test data
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $testCategoriesData = null;
        $testParamsForLog = [
            'consumer' => 'publish_categories',
            'message_body' => $testCategoriesData,
        ];
        
        // Mocking
        $amqpMessage
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($testCategoriesData);

        $this->loggerInterfaceMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(MessageBag::CATEGORIES_DATA_IS_BROKEN),
                $this->equalTo($testParamsForLog)
            );
        
        // Execution
        $methodResult = $this->categoryConsumerHandler->handlePublishCategories($amqpMessage);
        
        // Asserts
        $this->assertFalse($methodResult);
    }
}
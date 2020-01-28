<?php

namespace Tests\App\Service\QueueConsumerHandler;

use App\Service\ElasticSearch\ElasticSearchIndexManager;
use App\Service\HttpClientAdapter;
use App\Service\QueueConsumerHandler\CategoryConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CategoryConsumerHandlerTest
 *
 * @package Tests\App\Service\QueueConsumerHandler
 */
abstract class CategoryConsumerHandlerTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $elasticSearchIndexManagerMock;

    /**
     * @var MockObject
     */
    protected $loggerInterfaceMock;

    /**
     * @var MockObject
     */
    protected $eventDispatcherInterfaceMock;

    /**
     * @var MockObject
     */
    protected $httpClientAdapterMock;

    /**
     * @var string
     */
    protected $testCmsAdminApiCategoryPublishedUri;
    
    /**
     * @var CategoryConsumerHandler
     */
    protected $categoryConsumerHandler;
    
    /**
     * @var array
     */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->testCmsAdminApiCategoryPublishedUri = 'test';
        $this->elasticSearchIndexManagerMock = $this->createMock(ElasticSearchIndexManager::class);
        $this->loggerInterfaceMock = $this->createMock(LoggerInterface::class);
        $this->eventDispatcherInterfaceMock = $this->createMock(EventDispatcherInterface::class);
        $this->httpClientAdapterMock = $this->createMock(HttpClientAdapter::class);
        
        if ($this->mockMethods) {
            $this->categoryConsumerHandler = $this->getMockBuilder(CategoryConsumerHandler::class)
                ->setConstructorArgs([
                    $this->testCmsAdminApiCategoryPublishedUri,
                    $this->elasticSearchIndexManagerMock,
                    $this->loggerInterfaceMock,
                    $this->eventDispatcherInterfaceMock,
                    $this->httpClientAdapterMock
                ])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->categoryConsumerHandler = new CategoryConsumerHandler(
                $this->testCmsAdminApiCategoryPublishedUri,
                $this->elasticSearchIndexManagerMock,
                $this->loggerInterfaceMock,
                $this->eventDispatcherInterfaceMock,
                $this->httpClientAdapterMock
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->categoryConsumerHandler = null;
        gc_collect_cycles();
    }
}
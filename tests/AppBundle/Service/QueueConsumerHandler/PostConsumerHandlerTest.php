<?php

namespace Tests\App\Service\QueueConsumerHandler;

use App\Service\ElasticSearch\ElasticSearchIndexManager;
use App\Service\HttpClientAdapter;
use App\Service\QueueConsumerHandler\PostConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PostConsumerHandlerTest
 *
 * @package Tests\App\Service\QueueConsumerHandler
 */
abstract class PostConsumerHandlerTest extends TestCase
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
    protected $testCmsAdminApiPostPublishedUri;
    
    /**
     * @var PostConsumerHandler
     */
    protected $postConsumerHandler;
    
    /**
     * @var array
     */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->testCmsAdminApiPostPublishedUri = 'test';
        $this->elasticSearchIndexManagerMock = $this->createMock(ElasticSearchIndexManager::class);
        $this->loggerInterfaceMock = $this->createMock(LoggerInterface::class);
        $this->eventDispatcherInterfaceMock = $this->createMock(EventDispatcherInterface::class);
        $this->httpClientAdapterMock = $this->createMock(HttpClientAdapter::class);
        
        if ($this->mockMethods) {
            $this->postConsumerHandler = $this->getMockBuilder(PostConsumerHandler::class)
                ->setConstructorArgs([
                    $this->testCmsAdminApiPostPublishedUri,
                    $this->elasticSearchIndexManagerMock,
                    $this->loggerInterfaceMock,
                    $this->eventDispatcherInterfaceMock,
                    $this->httpClientAdapterMock
                ])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->postConsumerHandler = new PostConsumerHandler(
                $this->testCmsAdminApiPostPublishedUri,
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
        $this->postConsumerHandler = null;
        gc_collect_cycles();
    }
}
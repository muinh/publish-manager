<?php

namespace Tests\App\Service\QueueConsumerHandler;

use App\Service\ElasticSearch\ElasticSearchIndexManager;
use App\Service\HttpClientAdapter;
use App\Service\QueueConsumerHandler\AnalyticScriptConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AnalyticScriptConsumerHandlerTest
 *
 * @package Tests\App\Service\QueueConsumerHandler
 */
abstract class AnalyticScriptConsumerHandlerTest extends TestCase
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
    protected $testCmsAdminApiAnalyticScriptPublishedUri;
    
    /**
     * @var AnalyticScriptConsumerHandler
     */
    protected $analyticScriptConsumerHandler;
    
    /**
     * @var array
     */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->testCmsAdminApiAnalyticScriptPublishedUri = 'test';
        $this->elasticSearchIndexManagerMock = $this->createMock(ElasticSearchIndexManager::class);
        $this->loggerInterfaceMock = $this->createMock(LoggerInterface::class);
        $this->eventDispatcherInterfaceMock = $this->createMock(EventDispatcherInterface::class);
        $this->httpClientAdapterMock = $this->createMock(HttpClientAdapter::class);
        
        if ($this->mockMethods) {
            $this->analyticScriptConsumerHandler = $this->getMockBuilder(AnalyticScriptConsumerHandler::class)
                ->setConstructorArgs([
                    $this->testCmsAdminApiAnalyticScriptPublishedUri,
                    $this->elasticSearchIndexManagerMock,
                    $this->loggerInterfaceMock,
                    $this->eventDispatcherInterfaceMock,
                    $this->httpClientAdapterMock
                ])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->analyticScriptConsumerHandler = new AnalyticScriptConsumerHandler(
                $this->testCmsAdminApiAnalyticScriptPublishedUri,
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
        $this->analyticScriptConsumerHandler = null;
        gc_collect_cycles();
    }
}
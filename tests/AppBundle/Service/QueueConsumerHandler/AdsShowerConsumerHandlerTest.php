<?php

namespace Tests\App\Service\QueueConsumerHandler;

use App\Service\ElasticSearch\ElasticSearchIndexManager;
use App\Service\HttpClientAdapter;
use App\Service\QueueConsumerHandler\AdsShowerConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AdsShowerConsumerHandlerTest
 *
 * @package Tests\App\Service\QueueConsumerHandler
 */
abstract class AdsShowerConsumerHandlerTest extends TestCase
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
    protected $testCmsAdminApiAdsShowerPublishedUri;
    
    /**
     * @var AdsShowerConsumerHandler
     */
    protected $adsShowerConsumerHandler;
    
    /**
     * @var array
     */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->testCmsAdminApiAdsShowerPublishedUri = 'test';
        $this->elasticSearchIndexManagerMock = $this->createMock(ElasticSearchIndexManager::class);
        $this->loggerInterfaceMock = $this->createMock(LoggerInterface::class);
        $this->eventDispatcherInterfaceMock = $this->createMock(EventDispatcherInterface::class);
        $this->httpClientAdapterMock = $this->createMock(HttpClientAdapter::class);
        
        if ($this->mockMethods) {
            $this->adsShowerConsumerHandler = $this->getMockBuilder(AdsShowerConsumerHandler::class)
                ->setConstructorArgs([
                    $this->testCmsAdminApiAdsShowerPublishedUri,
                    $this->elasticSearchIndexManagerMock,
                    $this->loggerInterfaceMock,
                    $this->eventDispatcherInterfaceMock,
                    $this->httpClientAdapterMock
                ])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->adsShowerConsumerHandler = new AdsShowerConsumerHandler(
                $this->testCmsAdminApiAdsShowerPublishedUri,
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
        $this->adsShowerConsumerHandler = null;
        gc_collect_cycles();
    }
}
<?php

namespace Tests\App\Consumer;

use App\Consumer\PublishedAnalyticScriptConsumer;
use App\Service\QueueConsumerHandler\AnalyticScriptConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PublishedAdsShowerConsumerTest
 *
 * @package Tests\App\Consumer
 */
abstract class PublishedAnalyticScriptConsumerTest extends TestCase
{
    /** @var MockObject */
    protected $analyticScriptConsumerHandler;
    
    /** @var PublishedAnalyticScriptConsumer */
    protected $publishedAnalyticScriptConsumer;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->analyticScriptConsumerHandler = $this->createMock(AnalyticScriptConsumerHandler::class);
        
        if ($this->mockMethods) {
            $this->publishedAnalyticScriptConsumer = $this->getMockBuilder(PublishedAnalyticScriptConsumer::class)
                ->setConstructorArgs([$this->analyticScriptConsumerHandler])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->publishedAnalyticScriptConsumer = new PublishedAnalyticScriptConsumer($this->analyticScriptConsumerHandler);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->publishedAnalyticScriptConsumer = null;
        gc_collect_cycles();
    }
}
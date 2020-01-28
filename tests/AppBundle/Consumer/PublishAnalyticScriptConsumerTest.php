<?php

namespace Tests\App\Consumer;

use App\Consumer\PublishAnalyticScriptConsumer;
use App\Service\QueueConsumerHandler\AnalyticScriptConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PublishedAdsShowerConsumerTest
 *
 * @package Tests\App\Consumer
 */
abstract class PublishAnalyticScriptConsumerTest extends TestCase
{
    /** @var MockObject */
    protected $analyticScriptConsumerHandler;
    
    /** @var PublishAnalyticScriptConsumer */
    protected $publishAnalyticScriptConsumer;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->analyticScriptConsumerHandler = $this->createMock(AnalyticScriptConsumerHandler::class);
        
        if ($this->mockMethods) {
            $this->publishAnalyticScriptConsumer = $this->getMockBuilder(PublishAnalyticScriptConsumer::class)
                ->setConstructorArgs([$this->analyticScriptConsumerHandler])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->publishAnalyticScriptConsumer = new PublishAnalyticScriptConsumer($this->analyticScriptConsumerHandler);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->publishAnalyticScriptConsumer = null;
        gc_collect_cycles();
    }
}
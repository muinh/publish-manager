<?php

namespace Tests\App\Consumer;

use App\Consumer\PublishAdsShowerConsumer;
use App\Service\QueueConsumerHandler\AdsShowerConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PublishAdsShowerConsumerTest
 *
 * @package Tests\App\Consumer
 */
abstract class PublishAdsShowerConsumerTest extends TestCase
{
    /** @var MockObject */
    protected $adsShowerConsumerHandler;
    
    /** @var PublishAdsShowerConsumer */
    protected $publishAdsShowerConsumer;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->adsShowerConsumerHandler = $this->createMock(AdsShowerConsumerHandler::class);
        
        if ($this->mockMethods) {
            $this->publishAdsShowerConsumer = $this->getMockBuilder(PublishAdsShowerConsumer::class)
                ->setConstructorArgs([$this->adsShowerConsumerHandler])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->publishAdsShowerConsumer = new PublishAdsShowerConsumer($this->adsShowerConsumerHandler);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->publishAdsShowerConsumer = null;
        gc_collect_cycles();
    }
}
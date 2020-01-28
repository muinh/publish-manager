<?php

namespace Tests\App\Consumer;

use App\Consumer\PublishedAdsShowerConsumer;
use App\Service\QueueConsumerHandler\AdsShowerConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PublishedAdsShowerConsumerTest
 *
 * @package Tests\App\Consumer
 */
abstract class PublishedAdsShowerConsumerTest extends TestCase
{
    /** @var MockObject */
    protected $adsShowerConsumerHandler;
    
    /** @var PublishedAdsShowerConsumer */
    protected $publishedAdsShowerConsumer;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->adsShowerConsumerHandler = $this->createMock(AdsShowerConsumerHandler::class);
        
        if ($this->mockMethods) {
            $this->publishedAdsShowerConsumer = $this->getMockBuilder(PublishedAdsShowerConsumer::class)
                ->setConstructorArgs([$this->adsShowerConsumerHandler])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->publishedAdsShowerConsumer = new PublishedAdsShowerConsumer($this->adsShowerConsumerHandler);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->publishedAdsShowerConsumer = null;
        gc_collect_cycles();
    }
}
<?php

namespace Tests\App\Service\QueuePublisher;

use App\Service\QueuePublisher\AdsShowerPublisher;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class AdsShowerPublisherTest
 *
 * @package Tests\App\Service\QueuePublisher
 */
abstract class AdsShowerPublisherTest extends TestCase
{
    /** @var MockObject */
    protected $publishAdsShowerProducerInterfaceMock;

    /** @var MockObject */
    protected $publishedAdsShowerProducerInterfaceMock;
    
    /** @var MockObject */
    protected $serializerInterfaceMock;
    
    /** @var AdsShowerPublisher */
    protected $adsShowerPublisher;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->publishAdsShowerProducerInterfaceMock = $this->createMock(ProducerInterface::class);
        $this->publishedAdsShowerProducerInterfaceMock = $this->createMock(ProducerInterface::class);
        $this->serializerInterfaceMock = $this->createMock(SerializerInterface::class);
        
        if ($this->mockMethods) {
            $this->adsShowerPublisher = $this->getMockBuilder(AdsShowerPublisher::class)
                ->setConstructorArgs([
                    $this->publishAdsShowerProducerInterfaceMock,
                    $this->publishedAdsShowerProducerInterfaceMock,
                    $this->serializerInterfaceMock
                ])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->adsShowerPublisher = new AdsShowerPublisher(
                $this->publishAdsShowerProducerInterfaceMock,
                $this->publishedAdsShowerProducerInterfaceMock,
                $this->serializerInterfaceMock
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->adsShowerPublisher = null;
        gc_collect_cycles();
    }
}

<?php

namespace Tests\App\Service\QueuePublisher;

use App\Service\QueuePublisher\AnalyticScriptPublisher;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class AnalyticScriptPublisherTest
 *
 * @package Tests\App\Service\QueuePublisher
 */
abstract class AnalyticScriptPublisherTest extends TestCase
{
    /** @var MockObject */
    protected $publishAnalyticScriptProducerInterfaceMock;

    /** @var MockObject */
    protected $publishedAnalyticScriptProducerInterfaceMock;
    
    /** @var MockObject */
    protected $serializerInterfaceMock;
    
    /** @var AnalyticScriptPublisher */
    protected $analyticScriptPublisher;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->publishAnalyticScriptProducerInterfaceMock = $this->createMock(ProducerInterface::class);
        $this->publishedAnalyticScriptProducerInterfaceMock = $this->createMock(ProducerInterface::class);
        $this->serializerInterfaceMock = $this->createMock(SerializerInterface::class);
        
        if ($this->mockMethods) {
            $this->analyticScriptPublisher = $this->getMockBuilder(AnalyticScriptPublisher::class)
                ->setConstructorArgs([
                    $this->publishAnalyticScriptProducerInterfaceMock,
                    $this->publishedAnalyticScriptProducerInterfaceMock,
                    $this->serializerInterfaceMock
                ])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->analyticScriptPublisher = new AnalyticScriptPublisher(
                $this->publishAnalyticScriptProducerInterfaceMock,
                $this->publishedAnalyticScriptProducerInterfaceMock,
                $this->serializerInterfaceMock
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->analyticScriptPublisher = null;
        gc_collect_cycles();
    }
}

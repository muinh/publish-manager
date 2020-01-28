<?php

namespace Tests\App\Service\QueuePublisher;

use App\Service\QueuePublisher\PostPublisher;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class PostPublisherTest
 *
 * @package Tests\App\Service\QueuePublisher
 */
abstract class PostPublisherTest extends TestCase
{
    /** @var MockObject */
    protected $publishPostProducerInterfaceMock;

    /** @var MockObject */
    protected $publishedPostProducerInterfaceMock;
    
    /** @var MockObject */
    protected $serializerInterfaceMock;
    
    /** @var PostPublisher */
    protected $postPublisher;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->publishPostProducerInterfaceMock = $this->createMock(ProducerInterface::class);
        $this->publishedPostProducerInterfaceMock = $this->createMock(ProducerInterface::class);
        $this->serializerInterfaceMock = $this->createMock(SerializerInterface::class);
        
        if ($this->mockMethods) {
            $this->postPublisher = $this->getMockBuilder(PostPublisher::class)
                ->setConstructorArgs([
                    $this->publishPostProducerInterfaceMock,
                    $this->publishedPostProducerInterfaceMock,
                    $this->serializerInterfaceMock
                ])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->postPublisher = new PostPublisher(
                $this->publishPostProducerInterfaceMock,
                $this->publishedPostProducerInterfaceMock,
                $this->serializerInterfaceMock
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->postPublisher = null;
        gc_collect_cycles();
    }
}

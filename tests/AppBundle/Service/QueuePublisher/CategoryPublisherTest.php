<?php

namespace Tests\App\Service\QueuePublisher;

use App\Service\QueuePublisher\CategoryPublisher;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class CategoryPublisherTest
 *
 * @package Tests\App\Service\QueuePublisher
 */
abstract class CategoryPublisherTest extends TestCase
{
    /** @var MockObject */
    protected $publishCategoryProducerInterfaceMock;

    /** @var MockObject */
    protected $publishedCategoryProducerInterfaceMock;
    
    /** @var MockObject */
    protected $serializerInterfaceMock;
    
    /** @var CategoryPublisher */
    protected $categoryPublisher;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->publishCategoryProducerInterfaceMock = $this->createMock(ProducerInterface::class);
        $this->publishedCategoryProducerInterfaceMock = $this->createMock(ProducerInterface::class);
        $this->serializerInterfaceMock = $this->createMock(SerializerInterface::class);
        
        if ($this->mockMethods) {
            $this->categoryPublisher = $this->getMockBuilder(CategoryPublisher::class)
                ->setConstructorArgs([
                    $this->publishCategoryProducerInterfaceMock,
                    $this->publishedCategoryProducerInterfaceMock,
                    $this->serializerInterfaceMock
                ])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->categoryPublisher = new CategoryPublisher(
                $this->publishCategoryProducerInterfaceMock,
                $this->publishedCategoryProducerInterfaceMock,
                $this->serializerInterfaceMock
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->categoryPublisher = null;
        gc_collect_cycles();
    }
}

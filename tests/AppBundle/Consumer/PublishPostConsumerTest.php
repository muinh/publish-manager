<?php

namespace Tests\App\Consumer;

use App\Consumer\PublishPostConsumer;
use App\Service\QueueConsumerHandler\PostConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PublishPostConsumerTest
 *
 * @package Tests\App\Consumer
 */
abstract class PublishPostConsumerTest extends TestCase
{
    /** @var MockObject */
    protected $postConsumerHandler;
    
    /** @var PublishPostConsumer */
    protected $publishPostConsumer;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->postConsumerHandler = $this->createMock(PostConsumerHandler::class);
        
        if ($this->mockMethods) {
            $this->publishPostConsumer = $this->getMockBuilder(PublishPostConsumer::class)
                ->setConstructorArgs([$this->postConsumerHandler])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->publishPostConsumer = new PublishPostConsumer($this->postConsumerHandler);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->publishPostConsumer = null;
        gc_collect_cycles();
    }
}
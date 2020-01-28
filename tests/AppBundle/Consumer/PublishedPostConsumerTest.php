<?php

namespace Tests\App\Consumer;

use App\Consumer\PublishedPostConsumer;
use App\Service\QueueConsumerHandler\PostConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PublishedPostConsumerTest
 *
 * @package Tests\App\Consumer
 */
abstract class PublishedPostConsumerTest extends TestCase
{
    /** @var MockObject */
    protected $postConsumerHandler;
    
    /** @var PublishedPostConsumer */
    protected $publishedPostConsumer;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->postConsumerHandler = $this->createMock(PostConsumerHandler::class);
        
        if ($this->mockMethods) {
            $this->publishedPostConsumer = $this->getMockBuilder(PublishedPostConsumer::class)
                ->setConstructorArgs([$this->postConsumerHandler])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->publishedPostConsumer = new PublishedPostConsumer($this->postConsumerHandler);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->publishedPostConsumer = null;
        gc_collect_cycles();
    }
}
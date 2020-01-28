<?php

namespace Tests\App\Consumer;

use App\Consumer\PublishCategoriesConsumer;
use App\Service\QueueConsumerHandler\CategoryConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PublishCategoriesConsumerTest
 *
 * @package Tests\App\Consumer
 */
abstract class PublishCategoriesConsumerTest extends TestCase
{
    /** @var MockObject */
    protected $categoryConsumerHandler;
    
    /** @var PublishCategoriesConsumer */
    protected $publishCategoriesConsumer;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->categoryConsumerHandler = $this->createMock(CategoryConsumerHandler::class);
        
        if ($this->mockMethods) {
            $this->publishCategoriesConsumer = $this->getMockBuilder(PublishCategoriesConsumer::class)
                ->setConstructorArgs([$this->categoryConsumerHandler])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->publishCategoriesConsumer = new PublishCategoriesConsumer($this->categoryConsumerHandler);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->publishCategoriesConsumer = null;
        gc_collect_cycles();
    }
}
<?php

namespace Tests\App\Consumer;

use App\Consumer\PublishedCategoriesConsumer;
use App\Service\QueueConsumerHandler\CategoryConsumerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PublishCategoriesConsumerTest
 *
 * @package Tests\App\Consumer
 */
abstract class PublishedCategoriesConsumerTest extends TestCase
{
    /** @var MockObject */
    protected $categoryConsumerHandler;
    
    /** @var PublishedCategoriesConsumer */
    protected $publishedCategoriesConsumer;
    
    /** @var array */
    protected $mockMethods;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->categoryConsumerHandler = $this->createMock(CategoryConsumerHandler::class);
        
        if ($this->mockMethods) {
            $this->publishedCategoriesConsumer = $this->getMockBuilder(PublishedCategoriesConsumer::class)
                ->setConstructorArgs([$this->categoryConsumerHandler])
                ->setMethods($this->mockMethods)
                ->getMock();
        } else {
            $this->publishedCategoriesConsumer = new PublishedCategoriesConsumer($this->categoryConsumerHandler);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->publishedCategoriesConsumer = null;
        gc_collect_cycles();
    }
}
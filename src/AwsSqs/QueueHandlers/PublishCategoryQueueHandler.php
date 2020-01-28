<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\CategoryService;

/**
 * Class PublishCategoryQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishCategoryQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * PublishCategoryQueueHandler constructor.
     *
     * @codeCoverageIgnore
     *
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(string $queueName) : bool
    {
        return $queueName === SqsQueuesBag::PUBLISH_CATEGORIES;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $categoryData = json_decode($message->getBody(), true);

        return $this->categoryService->handlePublishCategoryEvent($categoryData);
    }
}

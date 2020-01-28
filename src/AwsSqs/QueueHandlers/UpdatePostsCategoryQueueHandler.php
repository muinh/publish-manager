<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\PostCategoriesService;

/**
 * Class UpdatePostsCategoryQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class UpdatePostsCategoryQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var PostCategoriesService
     */
    private $postCategoriesService;

    /**
     * UpdatePostsCategoryQueueHandler constructor.
     *
     * @codeCoverageIgnore
     *
     * @param PostCategoriesService $postCategoriesService
     */
    public function __construct(PostCategoriesService $postCategoriesService)
    {
        $this->postCategoriesService = $postCategoriesService;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(string $queueName) : bool
    {
        return $queueName === SqsQueuesBag::UPDATE_POSTS_CATEGORY;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $newCategory = json_decode($message->getBody(), true);
        $this->postCategoriesService->updatePostsCategory($newCategory);

        return true;
    }
}

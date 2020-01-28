<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\PostService;

/**
 * Class PublishedPostQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishedPostQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var PostService
     */
    private $postService;

    /**
     * PublishedPostQueueHandler constructor.
     *
     * @codeCoverageIgnore
     *
     * @param PostService $postService
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(string $queueName) : bool
    {
        return $queueName === SqsQueuesBag::PUBLISHED_POST;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $postData = json_decode($message->getBody(), true);

        return $this->postService->handlePublishedPostEvent($postData);
    }
}

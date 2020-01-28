<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\FakeAuthorService;

/**
 * Class PublishedFakeAuthorQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishedFakeAuthorQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var FakeAuthorService
     */
    private $fakeAuthorService;

    /**
     * PublishedFakeAuthorQueueHandler constructor.
     *
     * @codeCoverageIgnore
     *
     * @param FakeAuthorService $fakeAuthorService
     */
    public function __construct(FakeAuthorService $fakeAuthorService)
    {
        $this->fakeAuthorService = $fakeAuthorService;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(string $queueName) : bool
    {
        return $queueName === SqsQueuesBag::PUBLISHED_FAKE_AUTHOR;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $fakeAuthorData = json_decode($message->getBody(), true);

        return $this->fakeAuthorService->handlePublishedFakeAuthorEvent($fakeAuthorData);
    }
}
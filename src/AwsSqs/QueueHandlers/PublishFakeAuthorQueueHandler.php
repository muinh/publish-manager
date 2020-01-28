<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueueHandlerInterface, SqsQueuesBag};
use App\Service\FakeAuthorService;

/**
 * Class PublishFakeAuthorQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishFakeAuthorQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var FakeAuthorService
     */
    private $fakeAuthorService;

    /**
     * PublishFakeAuthorQueueHandler constructor.
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
        return $queueName === SqsQueuesBag::PUBLISH_FAKE_AUTHOR;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $fakeAuthorData = json_decode($message->getBody(), true);

        return $this->fakeAuthorService->handlePublishFakeAuthorEvent($fakeAuthorData);
    }
}
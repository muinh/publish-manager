<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\SeoTagService;

/**
 * Class PublishSeoTagsQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishSeoTagsQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var SeoTagService
     */
    private $seoTagService;

    /**
     * PublishSeoTagsQueueHandler constructor.
     *
     * @codeCoverageIgnore
     *
     * @param SeoTagService $seoTagService
     */
    public function __construct(SeoTagService $seoTagService)
    {
        $this->seoTagService = $seoTagService;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(string $queueName) : bool
    {
        return $queueName === SqsQueuesBag::PUBLISH_SEO_TAGS;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $seoTagsData = json_decode($message->getBody(), true);

        return $this->seoTagService->handlePublishSeoTagsEvent($seoTagsData);
    }
}

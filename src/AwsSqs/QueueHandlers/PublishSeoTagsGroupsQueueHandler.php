<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\SeoTagsGroupsService;

/**
 * Class PublishSeoTagsGroupsQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishSeoTagsGroupsQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var SeoTagsGroupsService
     */
    private $seoTagsGroupsService;

    /**
     * PublishSeoTagsGroupsQueueHandler constructor.
     *
     * @codeCoverageIgnore
     *
     * @param SeoTagsGroupsService $seoTagsGroupsService
     */
    public function __construct(SeoTagsGroupsService $seoTagsGroupsService)
    {
        $this->seoTagsGroupsService = $seoTagsGroupsService;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(string $queueName) : bool
    {
        return $queueName === SqsQueuesBag::PUBLISH_SEO_TAGS_GROUPS;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $seoTagsGroupsData = json_decode($message->getBody(), true);

        return $this->seoTagsGroupsService->handlePublishSeoTagsGroupsEvent($seoTagsGroupsData);
    }
}

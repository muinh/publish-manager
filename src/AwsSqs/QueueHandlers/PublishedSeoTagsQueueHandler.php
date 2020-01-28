<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\SeoTagService;

/**
 * Class PublishedSeoTagsQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishedSeoTagsQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var SeoTagService
     */
    private $seoTagService;

    /**
     * PublishedSeoTagsQueueHandler constructor.
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
        return $queueName === SqsQueuesBag::PUBLISHED_SEO_TAGS;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $seoTagsData = json_decode($message->getBody(), true);

        return $this->seoTagService->handlePublishedSeoTagsEvent($seoTagsData);
    }
}

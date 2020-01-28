<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\AnalyticScriptService;

/**
 * Class PublishedAnalyticScriptQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishedAnalyticScriptQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var AnalyticScriptService
     */
    private $analyticScriptService;

    /**
     * PublishedAnalyticScriptQueueHandler constructor.
     *
     * @codeCoverageIgnore
     *
     * @param AnalyticScriptService $analyticScriptService
     */
    public function __construct(AnalyticScriptService $analyticScriptService)
    {
        $this->analyticScriptService = $analyticScriptService;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(string $queueName) : bool
    {
        return $queueName === SqsQueuesBag::PUBLISHED_ANALYTIC_SCRIPT;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $analyticScriptData = json_decode($message->getBody(), true);

        return $this->analyticScriptService->handlePublishedAnalyticScriptEvent($analyticScriptData);
    }
}

<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\AnalyticScriptService;

/**
 * Class PublishAnalyticScriptQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishAnalyticScriptQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var AnalyticScriptService
     */
    private $analyticScriptService;

    /**
     * PublishAnalyticScriptQueueHandler constructor.
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
        return $queueName === SqsQueuesBag::PUBLISH_ANALYTIC_SCRIPT;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $analyticScriptData = json_decode($message->getBody(), true);

        return $this->analyticScriptService->handlePublishAnalyticScriptEvent($analyticScriptData);
    }
}

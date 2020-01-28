<?php

namespace App\AwsSqs\QueuePublishers;

use App\AwsSqs\SqsQueuesBag;

/**
 * Class AnalyticScriptPublisher
 *
 * @package App\AwsSqs\QueuePublishers
 */
class AnalyticScriptPublisher extends AbstractSqsQueuePublisher
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publish(array $analyticScriptData) : bool
    {
        return $this->publishMessage($analyticScriptData, SqsQueuesBag::PUBLISH_ANALYTIC_SCRIPT);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publishPublished(array $analyticScriptData) : bool
    {
        return $this->publishMessage($analyticScriptData, SqsQueuesBag::PUBLISHED_ANALYTIC_SCRIPT);
    }
}

<?php

namespace App\AwsSqs\QueuePublishers;

use App\AwsSqs\SqsQueuesBag;

/**
 * Class AdsShowerPublisher
 *
 * @package App\AwsSqs\QueuePublishers
 */
class AdsShowerPublisher extends AbstractSqsQueuePublisher
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publish(array $adsShowerData) : bool
    {
        return $this->publishMessage($adsShowerData, SqsQueuesBag::PUBLISH_ADS_SHOWER);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publishPublished(array $adsShowerData) : bool
    {
        return $this->publishMessage($adsShowerData, SqsQueuesBag::PUBLISHED_ADS_SHOWER);
    }
}

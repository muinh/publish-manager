<?php

namespace App\AwsSqs\QueuePublishers;

use App\AwsSqs\SqsQueuesBag;

/**
 * Class ConfigPublisher
 *
 * @package App\AwsSqs\QueuePublishers
 */
class ConfigPublisher extends AbstractSqsQueuePublisher
{
    /**
     * {@inheritdoc}
     */
    public function publish(array $config) : bool
    {
        return $this->publishMessage($config, SqsQueuesBag::PUBLISH_CONFIG);
    }

    /**
     * {@inheritdoc}
     */
    public function publishPublished(array $config) : bool
    {
        return $this->publishMessage($config, SqsQueuesBag::PUBLISHED_CONFIG);
    }
}
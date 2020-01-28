<?php

namespace App\AwsSqs\QueuePublishers;

use App\AwsSqs\SqsQueuesBag;

/**
 * Class PostPublisher
 *
 * @package App\AwsSqs\QueuePublishers
 */
class PostPublisher extends AbstractSqsQueuePublisher
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publish(array $postData) : bool
    {
        return $this->publishMessage($postData, SqsQueuesBag::PUBLISH_POST);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publishPublished(array $postData) : bool
    {
        return $this->publishMessage($postData, SqsQueuesBag::PUBLISHED_POST);
    }
}

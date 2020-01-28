<?php

namespace App\AwsSqs\QueuePublishers;

use App\AwsSqs\SqsQueuesBag;

/**
 * Class CategoryPublisher
 *
 * @package App\AwsSqs\QueuePublishers
 */
class CategoryPublisher extends AbstractSqsQueuePublisher
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publish(array $categoryData) : bool
    {
        return $this->publishMessage($categoryData, SqsQueuesBag::PUBLISH_CATEGORIES);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publishPublished(array $categoryData) : bool
    {
        return $this->publishMessage($categoryData, SqsQueuesBag::PUBLISHED_CATEGORIES);
    }
}

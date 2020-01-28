<?php

namespace App\AwsSqs\QueuePublishers;

/**
 * Interface SqsQueuePublisherInterface
 *
 * @package App\AwsSqs\QueuePublishers
 */
interface SqsQueuePublisherInterface
{
    /**
     * Publish data to queue.
     *
     * @param array $data
     * @return bool Is published
     */
    public function publish(array $data) : bool;

    /**
     * Publish data to published queue.
     *
     * @param array $data
     * @return bool Is published
     */
    public function publishPublished(array $data) : bool;
}

<?php

namespace App\AwsSqs\QueuePublishers;

use App\AwsSqs\SqsQueuesBag;

/**
 * Class FakeAuthorPublisher
 *
 * @package App\AwsSqs\QueuePublishers
 */
class FakeAuthorPublisher extends AbstractSqsQueuePublisher
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publish(array $fakeAuthorData) : bool
    {
        return $this->publishMessage($fakeAuthorData, SqsQueuesBag::PUBLISH_FAKE_AUTHOR);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publishPublished(array $fakeAuthorData) : bool
    {
        return $this->publishMessage($fakeAuthorData, SqsQueuesBag::PUBLISHED_FAKE_AUTHOR);
    }
}
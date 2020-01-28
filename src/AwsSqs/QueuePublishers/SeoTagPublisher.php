<?php

namespace App\AwsSqs\QueuePublishers;

use App\AwsSqs\SqsQueuesBag;

/**
 * Class SeoTagPublisher
 *
 * @package App\AwsSqs\QueuePublishers
 */
class SeoTagPublisher extends AbstractSqsQueuePublisher
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publish(array $seoTags) : bool
    {
        return $this->publishMessage($seoTags, SqsQueuesBag::PUBLISH_SEO_TAGS);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publishPublished(array $seoTags) : bool
    {
        return $this->publishMessage($seoTags, SqsQueuesBag::PUBLISHED_SEO_TAGS);
    }
}

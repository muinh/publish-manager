<?php

namespace App\AwsSqs\QueuePublishers;

use App\AwsSqs\SqsQueuesBag;

/**
 * Class SeoTagsGroupPublisher
 *
 * @package App\AwsSqs\QueuePublishers
 */
class SeoTagsGroupPublisher extends AbstractSqsQueuePublisher
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publish(array $seoTagsGroups) : bool
    {
        return $this->publishMessage($seoTagsGroups, SqsQueuesBag::PUBLISH_SEO_TAGS_GROUPS);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function publishPublished(array $seoTagsGroups) : bool
    {
        return $this->publishMessage($seoTagsGroups, SqsQueuesBag::PUBLISHED_SEO_TAGS_GROUPS);
    }
}

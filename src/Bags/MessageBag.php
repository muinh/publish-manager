<?php

namespace App\Bags;

/**
 * Class MessageBag
 *
 * @package App\Bags
 */
class MessageBag
{
    public const POST_NOT_RECEIVED = 'Post is not received.';
    public const POST_ID_NOT_RECEIVED = 'Post id is not received.';
    public const NO_POSTS_FOUNT_TO_UNPUBLISH = 'No posts found to unpublish by id [%s].';
    public const POST_REQUIRED_FIELDS_MISSING = 'Post data is not valid. Fields [%s] are missing.';
    public const POST_DATA_IS_BROKEN = 'Post data is broken.';
    public const FAILED_TO_ADD_POST_DATA_TO_INDEX = 'Failed to add post data to index.';
    public const FAILED_TO_SEND_POST_PUBLISHED_RESPONSE = 'Failed to send post published response.';
    public const FAILED_TO_PUBLISH_POST_TO_QUEUE = 'Failed to publish post to queue.';

    public const CATEGORY_REQUIRED_FIELDS_MISSING = 'Category data is not valid. Fields [%s] are missing.';
    public const CATEGORY_NOT_RECEIVED = 'Category is not received.';
    public const CATEGORY_DATA_IS_BROKEN = 'Category data is broken.';
    public const FAILED_TO_ADD_CATEGORY_DATA_TO_INDEX = 'Failed to add category data to index.';
    public const FAILED_TO_SEND_CATEGORY_PUBLISHED_RESPONSE = 'Failed to send category published response.';
    public const FAILED_TO_PUBLISH_CATEGORY_TO_QUEUE = 'Failed to publish category to queue.';

    public const SEO_TAG_REQUIRED_FIELDS_MISSING = 'Seo tag data is not valid. Fields [%s] are missing.';
    public const SEO_TAGS_NOT_RECEIVED = 'Seo tags are not received.';
    public const SEO_TAGS_DATA_IS_BROKEN = 'Seo tags data is broken.';
    public const FAILED_TO_ADD_SEO_TAGS_DATA_TO_INDEX = 'Failed to add seo tags data to index.';
    public const FAILED_TO_SEND_SEO_TAGS_PUBLISHED_RESPONSE = 'Failed to send seo tags published response.';
    public const FAILED_TO_PUBLISH_SEO_TAGS_TO_QUEUE = 'Failed to publish seo tags to queue.';

    public const SEO_TAGS_GROUP_REQUIRED_FIELDS_MISSING = 'Seo tags group data is not valid. Fields [%s] are missing.';
    public const SEO_TAGS_GROUPS_NOT_RECEIVED = 'Seo tags groups are not received.';
    public const SEO_TAGS_GROUPS_DATA_IS_BROKEN = 'Seo tags groups data is broken.';
    public const FAILED_TO_ADD_SEO_TAGS_GROUPS_DATA_TO_INDEX = 'Failed to add seo tags groups data to index.';
    public const FAILED_TO_SEND_SEO_TAGS_GROUPS_PUBLISHED_RESPONSE = 'Failed to send seo tags groups published response.';
    public const FAILED_TO_PUBLISH_SEO_TAG_GROUPS_TO_QUEUE = 'Failed to publish seo tag groups to queue.';

    public const ANALYTIC_SCRIPT_NOT_RECEIVED = 'Analytic script is not received.';
    public const ANALYTIC_SCRIPT_REQUIRED_FIELDS_MISSING = 'Analytic script data is not valid. Fields [%s] are missing.';
    public const ANALYTIC_SCRIPT_DATA_IS_BROKEN = 'Analytic script data is broken.';
    public const FAILED_TO_ADD_ANALYTIC_SCRIPT_DATA_TO_INDEX = 'Failed to add analytic script data to index.';
    public const FAILED_TO_SEND_ANALYTIC_SCRIPT_PUBLISHED_RESPONSE = 'Failed to send analytic script published response.';
    public const FAILED_TO_PUBLISH_ANALYTICS_SCRIPT_SHOWER_TO_QUEUE = 'Failed to publish ads analytics script to queue.';

    public const ADS_SHOWER_NOT_RECEIVED = 'Ads shower is not received.';
    public const ADS_SHOWER_REQUIRED_FIELDS_MISSING = 'Ads shower data is not valid. Fields [%s] are missing.';
    public const ADS_SHOWER_DATA_IS_BROKEN = 'Ads shower data is broken.';
    public const FAILED_TO_ADD_ADS_SHOWER_DATA_TO_INDEX = 'Failed to add ads shower data to index.';
    public const FAILED_TO_SEND_ADS_SHOWER_PUBLISHED_RESPONSE = 'Failed to send ads shower published response.';
    public const FAILED_TO_PUBLISH_ADS_SHOWER_TO_QUEUE = 'Failed to publish ads shower to queue.';

    public const INTERACTIVE_CONTENT_NOT_VALID = 'Interactive content not valid.';
    public const INTERACTIVE_CONTENT_NOT_RECEIVED = 'Interactive content not received.';
    public const FAILED_TO_ADD_INTERACTIVE_CONTENT_DATA_TO_INDEX = 'Failed to add interactive content data to index.';
    public const INTERACTIVE_CONTENT_TYPE_NOT_HANDLED = 'Interactive content type [%s] is not handled.';

    public const FAKE_AUTHOR_NOT_RECEIVED = 'Fake author is not received.';
    public const FAKE_AUTHOR_REQUIRED_FIELDS_MISSING = 'Fake author data is not valid. Fields [%s] are missing.';
    public const FAILED_TO_PUBLISH_FAKE_AUTHOR_TO_QUEUE = 'Failed to publish fake author to queue.';
    public const FAILED_TO_ADD_FAKE_AUTHOR_DATA_TO_INDEX = 'Failed to add fake author data to index.';
    public const FAKE_AUTHOR_DATA_IS_BROKEN = 'Fake author data is broken.';
    public const FAILED_TO_SEND_FAKE_AUTHOR_PUBLISHED_RESPONSE = 'Failed to send fake author published response.';
}

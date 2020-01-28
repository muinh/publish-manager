<?php

namespace App\AwsSqs;

/**
 * Class SqsQueuesBag
 *
 * @package App\AwsSqs
 */
class SqsQueuesBag
{
    public const PUBLISH_POST = 'publish_post';
    public const PUBLISHED_POST = 'published_post';

    public const PUBLISH_CATEGORIES = 'publish_categories';
    public const PUBLISHED_CATEGORIES = 'published_categories';

    public const PUBLISH_SEO_TAGS = 'publish_seo_tags';
    public const PUBLISHED_SEO_TAGS = 'published_seo_tags';

    public const PUBLISH_SEO_TAGS_GROUPS = 'publish_seo_tags_groups';
    public const PUBLISHED_SEO_TAGS_GROUPS = 'published_seo_tags_groups';

    public const PUBLISH_ADS_SHOWER = 'publish_ads_shower';
    public const PUBLISHED_ADS_SHOWER = 'published_ads_shower';

    public const PUBLISH_ANALYTIC_SCRIPT = 'publish_analytic_script';
    public const PUBLISHED_ANALYTIC_SCRIPT = 'published_analytic_script';

    public const PUBLISH_CONFIG = 'publish_config';
    public const PUBLISHED_CONFIG = 'published_config';

    public const PUBLISH_FAKE_AUTHOR = 'publish_fake_author';
    public const PUBLISHED_FAKE_AUTHOR = 'published_fake_author';

    public const UPDATE_POSTS_CATEGORY = 'update_posts_category';

    /**
     * List of available queues for project.
     */
    public const PUBLIC_QUEUES = [
        self::PUBLISH_POST,
        self::PUBLISHED_POST,
        self::PUBLISH_CATEGORIES,
        self::PUBLISHED_CATEGORIES,
        self::PUBLISH_ADS_SHOWER,
        self::PUBLISHED_ADS_SHOWER,
        self::PUBLISH_ANALYTIC_SCRIPT,
        self::PUBLISHED_ANALYTIC_SCRIPT,
        self::UPDATE_POSTS_CATEGORY,
        self::PUBLISH_SEO_TAGS,
        self::PUBLISHED_SEO_TAGS,
        self::PUBLISH_SEO_TAGS_GROUPS,
        self::PUBLISHED_SEO_TAGS_GROUPS,
        self::PUBLISH_CONFIG,
        self::PUBLISHED_CONFIG,
        self::PUBLISH_FAKE_AUTHOR,
        self::PUBLISHED_FAKE_AUTHOR
    ];
}

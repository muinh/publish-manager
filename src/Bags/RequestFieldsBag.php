<?php

namespace App\Bags;

/**
 * Class RequestDataBag
 *
 * @package App\Bags
 */
class RequestFieldsBag
{
    //todo: replace snake_case with camelCase everywhere
    public const PUBLISH_POST_DATA_FIELD = 'post';
    public const PUBLISH_CATEGORY_DATA_FIELD = 'category';
    public const PUBLISH_SEO_TAGS_DATA_FIELD = 'seo_tags';
    public const PUBLISH_SEO_TAGS_GROUPS_DATA_FIELD = 'seo_tags_groups';
    public const PUBLISH_ANALYTIC_SCRIPT_DATA_FIELD = 'analytic_script';
    public const PUBLISH_ADS_SHOWER_DATA_FIELD = 'ads_shower';
    public const PUBLISH_INTERACTIVE_CONTENT_DATA_FIELD = 'interactive_content';
    public const PUBLISH_CONFIG_DATA_FIELD = 'config';
    public const PUBLISH_FAKE_AUTHOR_DATA_FIELD = 'fakeAuthor';

    public const PUBLISHED_ANALYTIC_SCRIPT_ID_FIELD = 'analytic_script_id';
    public const PUBLISHED_CATEGORY_ID_FIELD = 'category_id';
    public const PUBLISHED_SEO_TAGS_IDS_FIELD = 'seo_tags_ids';
    public const PUBLISHED_SEO_TAGS_GROUPS_IDS_FIELD = 'seo_tags_groups_ids';
    public const PUBLISHED_POST_ID_FIELD = 'post_id';
    public const PUBLISHED_POST_PUBLISHED_AT_FIELD = 'post_published_at';
    public const PUBLISHED_POST_REPUBLISHED_AT_FIELD = 'post_republished_at';
    public const PUBLISHED_ADS_SHOWER_ID_FIELD = 'ads_shower_id';
    public const PUBLISHED_CONFIG_ID_FIELD = 'config_id';
    public const PUBLISHED_FAKE_AUTHOR_ID_FIELD = 'fakeAuthorId';

    public const UNPUBLISH_POST_ID_FIELD = 'post_id';
}

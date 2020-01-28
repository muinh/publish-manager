<?php

namespace App\Bags;

/**
 * Class ElasticSearchParametersBag
 *
 * @package App\Bags
 */
class ElasticSearchParametersBag
{
    public const TYPE_POST = 'post';
    public const TYPE_CATEGORY = 'category';
    public const TYPE_SEO_TAG = 'seo_tag';
    public const TYPE_SEO_TAGS_GROUP = 'seo_tags_group';
    public const TYPE_ANALYTIC_SCRIPT = 'analytic_script';
    public const TYPE_ADS_SHOWER = 'ads_shower';
    public const TYPE_CONFIG = 'config';
    public const TYPE_FAKE_AUTHOR = 'fake_author';

    public const ID_ANALYTIC_SCRIPT = 'config';
    public const ID_ADS_SHOWER = 'ad';

    public const ID_CONFIG = 'project_config';

    public const TYPES_LIST = [
        self::TYPE_POST,
        self::TYPE_CATEGORY,
        self::TYPE_SEO_TAG,
        self::TYPE_SEO_TAGS_GROUP,
        self::TYPE_ANALYTIC_SCRIPT,
        self::TYPE_ADS_SHOWER,
        self::TYPE_CONFIG,
        self::TYPE_FAKE_AUTHOR
    ];

    public const INDEX_INTERACTIVE_CONTENT = 'interactive_content';
    public const TYPE_FLIP_CARD = 'flip_card';
}
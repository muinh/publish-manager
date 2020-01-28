<?php

namespace Tests\App\Stub;

/**
 * Class MessageBag
 *
 * @package Tests\App\Bags
 */
class MessageBag
{
    public const POST_NOT_RECEIVED = 'Post is not received. Request parameter [post] is empty.';
    public const POST_REQUIRED_FIELDS_MISSING = 'Post data is not valid. Fields [%s] are missing.';
    public const POST_DATA_IS_BROKEN = 'Post data is broken.';
    public const FAILED_TO_ADD_POST_DATA_TO_INDEX = 'Failed to add post data to index.';
    public const FAILED_TO_SEND_POST_PUBLISHED_RESPONSE = 'Failed to send post published response.';

    public const CATEGORY_REQUIRED_FIELDS_MISSING = 'Category data is not valid. Fields [%s] are missing.';
    public const CATEGORIES_NOT_RECEIVED = 'Categories are not received. Request parameter [categories] is empty.';
    public const CATEGORIES_DATA_IS_BROKEN = 'Categories data is broken.';
    public const FAILED_TO_ADD_CATEGORIES_DATA_TO_INDEX = 'Failed to add categories data to index.';
    public const FAILED_TO_SEND_CATEGORIES_PUBLISHED_RESPONSE = 'Failed to send categories published response.';

    public const ANALYTIC_SCRIPT_NOT_RECEIVED = 'Analytic script is not received. Request parameter [%s] is empty.';
    public const ANALYTIC_SCRIPT_REQUIRED_FIELDS_MISSING = 'Analytic script data is not valid. Fields [%s] are missing.';
    public const ANALYTIC_SCRIPT_DATA_IS_BROKEN = 'Analytic script data is broken.';
    public const FAILED_TO_ADD_ANALYTIC_SCRIPT_DATA_TO_INDEX = 'Failed to add analytic script data to index.';
    public const FAILED_TO_SEND_ANALYTIC_SCRIPT_PUBLISHED_RESPONSE = 'Failed to send analytic script published response.';
    
    public const ADS_SHOWER_NOT_RECEIVED = 'Ads shower is not received. Request parameter [%s] is empty.';
    public const ADS_SHOWER_REQUIRED_FIELDS_MISSING = 'Ads shower data is not valid. Fields [%s] are missing.';
    public const ADS_SHOWER_DATA_IS_BROKEN = 'Ads shower data is broken.';
    public const FAILED_TO_ADD_ADS_SHOWER_DATA_TO_INDEX = 'Failed to add ads shower data to index.';
    public const FAILED_TO_SEND_ADS_SHOWER_PUBLISHED_RESPONSE = 'Failed to send ads shower published response.';
}

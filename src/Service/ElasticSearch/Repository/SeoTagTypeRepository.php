<?php

namespace App\Service\ElasticSearch\Repository;

use App\Bags\ElasticSearchParametersBag;

/**
 * Class SeoTagTypeRepository
 *
 * @package App\Service\ElasticSearch\Repository
 */
class SeoTagTypeRepository extends AbstractTypeRepository
{
    /**
     * {@inheritdoc}
     */
    public function getType() : string
    {
        return ElasticSearchParametersBag::TYPE_SEO_TAG;
    }
}
<?php

namespace App\Service\ElasticSearch\Repository;

use App\Bags\ElasticSearchParametersBag;

/**
 * Class CategoryTypeRepository
 *
 * @package App\Service\ElasticSearch\Repository
 */
class CategoryTypeRepository extends AbstractTypeRepository
{
    /**
     * {@inheritdoc}
     */
    public function getType() : string
    {
        return ElasticSearchParametersBag::TYPE_CATEGORY;
    }
}
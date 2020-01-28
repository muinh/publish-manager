<?php

namespace App\Service\ElasticSearch\Repository;

use App\Bags\ElasticSearchParametersBag;

/**
 * Class AdsShowerTypeRepository
 *
 * @package App\Service\ElasticSearch\Repository
 */
class AdsShowerTypeRepository extends AbstractTypeRepository
{
    /**
     * {@inheritdoc}
     */
    public function getType() : string
    {
        return ElasticSearchParametersBag::TYPE_ADS_SHOWER;
    }
}
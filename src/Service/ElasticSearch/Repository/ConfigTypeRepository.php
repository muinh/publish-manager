<?php

namespace App\Service\ElasticSearch\Repository;

use App\Bags\ElasticSearchParametersBag;

/**
 * Class ConfigTypeRepository
 *
 * @package App\Service\ElasticSearch\Repository
 */
class ConfigTypeRepository extends AbstractTypeRepository
{
    /**
     * {@inheritdoc}
     */
    public function getType() : string
    {
        return ElasticSearchParametersBag::TYPE_CONFIG;
    }
}
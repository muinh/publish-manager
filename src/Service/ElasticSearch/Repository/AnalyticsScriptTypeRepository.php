<?php

namespace App\Service\ElasticSearch\Repository;

use App\Bags\ElasticSearchParametersBag;

/**
 * Class AnalyticsScriptTypeRepository
 *
 * @package App\Service\ElasticSearch\Repository
 */
class AnalyticsScriptTypeRepository extends AbstractTypeRepository
{
    /**
     * {@inheritdoc}
     */
    public function getType() : string
    {
        return ElasticSearchParametersBag::TYPE_ANALYTIC_SCRIPT;
    }
}
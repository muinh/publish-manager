<?php

namespace App\Service\ElasticSearch\Repository;

use App\Bags\ElasticSearchParametersBag;

/**
 * Class FlipCardTypeRepository
 *
 * @package App\Service\ElasticSearch\Repository
 */
class FlipCardTypeRepository extends AbstractTypeRepository
{
    /**
     * Get project index.
     *
     * @return string
     */
    protected function getIndex() : string
    {
        return ElasticSearchParametersBag::INDEX_INTERACTIVE_CONTENT;
    }

    /**
     * {@inheritdoc}
     */
    public function getType() : string
    {
        return ElasticSearchParametersBag::TYPE_FLIP_CARD;
    }
}
<?php

namespace App\Service\ElasticSearch\Repository;

use App\Bags\ElasticSearchParametersBag;

/**
 * Class FakeAuthorTypeRepository
 *
 * @package App\Service\ElasticSearch\Repository
 */
class FakeAuthorTypeRepository extends AbstractTypeRepository
{
    /**
     * {@inheritdoc}
     */
    public function getType() : string
    {
        return ElasticSearchParametersBag::TYPE_FAKE_AUTHOR;
    }
}
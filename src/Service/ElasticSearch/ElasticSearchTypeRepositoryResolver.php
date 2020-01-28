<?php

namespace App\Service\ElasticSearch;

/**
 * Class ElasticSearchTypeRepositoryResolver
 *
 * @package App\Service\ElasticSearch
 */
class ElasticSearchTypeRepositoryResolver
{
    private const REPOSITORY_FOR_TYPE_IS_NOT_FOUND = 'Repository for type [%s] is not found';

    /**
     * @var TypeRepositoryInterface[]
     */
    private $repositories;

    /**
     * ElasticSearchTypeRepositoryResolver constructor.
     *
     * @param iterable $repositories
     */
    public function __construct(iterable $repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * Get repository for given type.
     *
     * @param string $type
     * @return TypeRepositoryInterface
     */
    public function resolve(string $type) : TypeRepositoryInterface
    {
        foreach ($this->repositories as $repository) {
            if ($repository->getType() === $type) {
                return $repository;
            }
        }

        throw new \RuntimeException(sprintf(self::REPOSITORY_FOR_TYPE_IS_NOT_FOUND, $type));
    }
}
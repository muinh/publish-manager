<?php

namespace App\Command;

use App\Bags\ElasticSearchParametersBag;
use App\Service\ElasticSearch\ElasticSearchTypeRepositoryResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class DeleteElasticSearchTypeIdCommand
 *
 * @package App\Command
 */
class DeleteElasticSearchTypeIdCommand extends Command
{
    public const NAME = 'app:elastica:delete-type-id';

    /**
     * @var ElasticSearchTypeRepositoryResolver
     */
    private $elasticSearchTypeRepositoryResolver;

    /**
     * DeleteElasticSearchTypeIdCommand constructor.
     *
     * @param ElasticSearchTypeRepositoryResolver $elasticSearchTypeRepositoryResolver
     * @throws LogicException
     */
    public function __construct(ElasticSearchTypeRepositoryResolver $elasticSearchTypeRepositoryResolver)
    {
        $this->elasticSearchTypeRepositoryResolver = $elasticSearchTypeRepositoryResolver;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Delete type id from elasticsearch');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $console = new SymfonyStyle($input, $output);

        $type = $console->choice('Select Elasticsearch type', ElasticSearchParametersBag::TYPES_LIST, null);
        $isId = $console->confirm('Do you have type ID');

        if ($isId) {
            $id = $console->ask('Please, type an ID that will be used to delete record');
        } else {
            $id = md5($console->ask('Please, type a string that will be hashed to get ID to delete record'));
        }

        $repository = $this->elasticSearchTypeRepositoryResolver->resolve($type);
        $response = $repository->execDeleteByTypeId($id);

        if ($response === []) {
            $console->warning(sprintf('Could not find record with ID : %s', $id));
        } else {
            $console->success(sprintf('Record [%s] has been successfully deleted from type [%s]', $id, $type));
        }
    }
}

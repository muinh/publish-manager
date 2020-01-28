<?php

namespace App\Command;

use App\Service\ElasticSearch\IndexService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class IndexManagerCommand
 *
 * @package App\Command
 */
class IndexManagerCommand extends Command
{
    public const NAME = 'app:index-manager';

    /**
     * @var IndexService
     */
    private $indexService;

    /**
     * IndexManagerCommand constructor.
     *
     * @param IndexService $indexService
     * @throws LogicException
     */
    public function __construct(IndexService $indexService)
    {
        $this->indexService = $indexService;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Manage indexes.')
            ->addOption('index', null, InputOption::VALUE_OPTIONAL, 'Index that will be used for current operation')
            ->addOption('backup')
            ->addOption('restore')
            ->addOption('create')
            ->addOption('delete');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $console = new SymfonyStyle($input, $output);

        if ($input->getOption('backup')) {
            $result = $this->indexService->backupIndex($input->getOption('index'));
            $console->writeln(var_export($result, true));
            $console->note('Backup finished');
        }

        if ($input->getOption('restore')) {
            $result = $this->indexService->restoreIndex($input->getOption('index'));
            $console->writeln(var_export($result, true));
            $console->note('Index data have been restored');
        }

        if ($input->getOption('create')) {
            $result = $this->indexService->createIndex($input->getOption('index'));
            $console->writeln(var_export($result, true));
            $console->note('Index has been created with a new mapping');
        }

        if ($input->getOption('delete')) {
            $result = $this->indexService->deleteIndex($input->getOption('index'));
            $console->writeln(var_export($result, true));
            $console->note('Index has been deleted');
        }
    }
}

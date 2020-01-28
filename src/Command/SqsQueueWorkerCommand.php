<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\{InvalidArgumentException, LogicException};
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\{RuntimeException, LogicException as ProcessLogicException};
use Symfony\Component\Process\Process;

/**
 * Class SqsQueueWorkerCommand
 *
 * @package App\Command
 */
class SqsQueueWorkerCommand extends Command
{
    public const NAME = 'app:sqs_queues:worker';
    private const SYMFONY_CONSOLE_PATH = 'bin/console';

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string
     */
    private $environment;

    /**
     * SqsQueueWorkerCommand constructor.
     *
     * @param string $projectDir
     * @param string $environment
     * @throws LogicException
     */
    public function __construct(string $projectDir, string $environment)
    {
        $this->projectDir = $projectDir;
        $this->environment = $environment;

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
            ->setDescription('Start a worker that will run consumer for SQS queues');
    }

    /**
     * {@inheritdoc}
     *
     * @throws ProcessLogicException
     * @throws RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parts = [
            rtrim($this->projectDir, '/ ') . '/' . self::SYMFONY_CONSOLE_PATH,
            SqsQueueConsumerCommand::NAME,
            '-e',
            $this->environment,
        ];

        $process = new Process(implode(' ', $parts));

        while (true) {
            $process->run();
        }
    }
}

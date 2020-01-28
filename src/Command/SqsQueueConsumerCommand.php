<?php

namespace App\Command;

use App\AwsSqs\SqsQueueWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\{InvalidArgumentException, LogicException};
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SqsQueueConsumerCommand
 *
 * @package App\Command
 */
class SqsQueueConsumerCommand extends Command
{
    public const NAME = 'app:sqs_queues:consumer';

    /**
     * @var SqsQueueWorker
     */
    private $sqsQueueWorker;

    /**
     * SqsQueueWorkerCommand constructor.
     *
     * @param SqsQueueWorker $sqsQueueWorker
     * @throws LogicException
     */
    public function __construct(SqsQueueWorker $sqsQueueWorker)
    {
        $this->sqsQueueWorker = $sqsQueueWorker;

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
            ->setDescription('Start a consumer that will listen to all SQS queues');
    }

    /**
     * {@inheritdoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sqsQueueWorker->start();
    }
}

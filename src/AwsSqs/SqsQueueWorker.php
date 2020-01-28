<?php

namespace App\AwsSqs;

use Psr\Log\LoggerInterface;

/**
 * Class SqsQueueWorker
 *
 * @package App\AwsSqs
 */
class SqsQueueWorker
{
    private const FAILED_TO_HANDLE_MESSAGE = 'Failed to handle message: %s';

    /**
     * @var SqsQueueManager
     */
    private $sqsQueueManager;

    /**
     * @var SqsQueueHandlerResolver
     */
    private $sqsQueueHandlerResolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SqsQueueWorker constructor.
     *
     * @param SqsQueueManager $sqsQueueManager
     * @param SqsQueueHandlerResolver $sqsQueueHandlerResolver
     * @param LoggerInterface $logger
     */
    public function __construct(
        SqsQueueManager $sqsQueueManager,
        SqsQueueHandlerResolver $sqsQueueHandlerResolver,
        LoggerInterface $logger
    ) {
        $this->sqsQueueManager = $sqsQueueManager;
        $this->sqsQueueHandlerResolver = $sqsQueueHandlerResolver;
        $this->logger = $logger;
    }

    /**
     * Start consumer for all queues.
     *
     * @throws \RuntimeException
     */
    public function start()
    {
        $this->consume();
    }

    /**
     * Consume messages.
     *
     * @throws \RuntimeException
     */
    private function consume()
    {
        $message = $this->sqsQueueManager->receiveMessage();

        if ($message !== null) {
            $queueHandler = $this->sqsQueueHandlerResolver->resolve($message->getQueueName());

            try {
                if ($queueHandler->handleMessage($message)) {
                    $this->sqsQueueManager->deleteMessage($message);
                }
            } catch (\Throwable $e) {
                $this->logger->critical(sprintf(self::FAILED_TO_HANDLE_MESSAGE, $e->getMessage()), [
                    'message' => json_encode($message),
                ]);
            }
        }
    }
}

<?php

namespace App\AwsSqs;

/**
 * Class SqsQueueHandlerResolver
 *
 * @package App\AwsSqs
 */
class SqsQueueHandlerResolver
{
    private const HANDLER_FOR_QUEUE_IS_NOT_FOUND = 'Handler for queue [%s] is not found';

    /**
     * @var SqsQueueHandlerInterface[]
     */
    private $queueHandlers;

    /**
     * SqsQueueHandlerResolver constructor.
     *
     * @param iterable $queueHandlers
     */
    public function __construct(iterable $queueHandlers)
    {
        $this->queueHandlers = $queueHandlers;
    }

    /**
     * Get handler for given queue name.
     *
     * @param string $queueName
     * @return SqsQueueHandlerInterface
     * @throws \RuntimeException
     */
    public function resolve(string $queueName) : SqsQueueHandlerInterface
    {
        foreach ($this->queueHandlers as $queueHandler) {
            if ($queueHandler->canHandle($queueName)) {
                return $queueHandler;
            }
        }

        throw new \RuntimeException(sprintf(self::HANDLER_FOR_QUEUE_IS_NOT_FOUND, $queueName));
    }
}

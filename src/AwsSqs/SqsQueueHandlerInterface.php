<?php

namespace App\AwsSqs;

use App\AwsSqs\Model\SqsMessage;

/**
 * Interface SqsQueueHandlerInterface
 *
 * @package App\AwsSqs
 */
interface SqsQueueHandlerInterface
{
    /**
     * Decide if current queue handler can handle messages from given queue.
     *
     * @param string $queueName
     * @return bool
     */
    public function canHandle(string $queueName) : bool;

    /**
     * Handle message from the queue.
     *
     * @param SqsMessage $message
     * @return bool TRUE if message was successfully handled, FALSE otherwise
     */
    public function handleMessage(SqsMessage $message) : bool;
}

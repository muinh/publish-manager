<?php

namespace App\AwsSqs\QueuePublishers;

use App\AwsSqs\SqsQueueManager;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class AbstractSqsQueuePublisher
 *
 * @package App\AwsSqs\QueuePublishers
 */
abstract class AbstractSqsQueuePublisher implements SqsQueuePublisherInterface
{
    /**
     * @var SqsQueueManager
     */
    private $sqsQueueManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * AbstractSqsQueuePublisher constructor.
     *
     * @codeCoverageIgnore
     *
     * @param SqsQueueManager $sqsQueueManager
     * @param SerializerInterface $serializer
     */
    public function __construct(SqsQueueManager $sqsQueueManager, SerializerInterface $serializer)
    {
        $this->sqsQueueManager = $sqsQueueManager;
        $this->serializer = $serializer;
    }

    /**
     * Publish message.
     *
     * @param array $data
     * @param string $queueName
     * @return bool Is published
     * @throws \RuntimeException
     */
    protected function publishMessage(array $data, string $queueName) : bool
    {
        $message = $this->serializer->serialize($data, 'json');

        return $this->sqsQueueManager->publishMessage($queueName, $message);
    }
}

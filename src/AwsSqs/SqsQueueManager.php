<?php

namespace App\AwsSqs;

use App\AwsSqs\Model\SqsMessage;
use Aws\Sqs\SqsClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SqsQueueManager
 *
 * @package App\AwsSqs
 */
class SqsQueueManager
{
    private const SQS_QUEUE_IS_NOT_DEFINED = 'SQS Queue [%s] is not defined.';

    /**
     * @var SqsClient
     */
    private $sqsClient;

    /**
     * @var string
     */
    private $sqsQueueEndpoint;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SqsQueueManager constructor.
     *
     * @param string $sqsQueueEndpoint
     * @param SqsClient $sqsClient
     * @param LoggerInterface $logger
     */
    public function __construct(string $sqsQueueEndpoint, SqsClient $sqsClient, LoggerInterface $logger)
    {
        $this->sqsQueueEndpoint = $sqsQueueEndpoint;
        $this->sqsClient = $sqsClient;
        $this->logger = $logger;
    }

    /**
     * Publish message.
     *
     * @param string $queueName
     * @param mixed $messageBody
     * @return bool Returns TRUE if message was published successfully, FALSE otherwise
     * @throws \RuntimeException
     */
    public function publishMessage(string $queueName, $messageBody) : bool
    {
        $this->validateQueueName($queueName);
        $encodedMessageBody = \is_string($messageBody) ? $messageBody : json_encode($messageBody);

        $params = [
            'MessageBody' => $encodedMessageBody,
            'QueueUrl' => $this->sqsQueueEndpoint,
        ];

        if ($this->isFifoQueue()) {
            $params['MessageGroupId'] = $queueName;
            $params['MessageDeduplicationId'] = $queueName . '_' . time();
        } else {
            $params['MessageAttributes'] = [
                'QueueName' => [
                    'DataType' => 'String',
                    'StringValue' => $queueName
                ],
            ];
        }

        try {
            $result = $this->sqsClient->sendMessage($params);

            return $result->hasKey('MessageId');
        } catch (\Throwable $e) {
            $this->logger->critical('Publish to queue error: ' . $e->getMessage(), [
                'request_data' => json_encode($params),
            ]);

            return false;
        }
    }

    /**
     * Receive message.
     *
     * @return SqsMessage|null Returns NULL if no messages in queue
     */
    public function receiveMessage() : ?SqsMessage
    {
        $params = [
            'AttributeNames' => [
                'SentTimestamp',
                'MessageGroupId'
            ],
            'MessageAttributeNames' => ['All'],
            'MaxNumberOfMessages' => 1,
            'QueueUrl' => $this->sqsQueueEndpoint,
        ];

        try {
            $result = $this->sqsClient->receiveMessage($params);

            if (\count($result->get('Messages')) > 0) {
                $message = $result->get('Messages')[0];

                return new SqsMessage($message);
            }

            return null;
        } catch (\Throwable $e) {
            $this->logger->critical('Receive message from queue error: ' . $e->getMessage(), [
                'request_data' => json_encode($params),
            ]);

            return null;
        }
    }

    /**
     * Delete message from the queue.
     *
     * @param SqsMessage $message
     * @return bool Returns TRUE if message was deleted successfully, FALSE otherwise
     */
    public function deleteMessage(SqsMessage $message) : bool
    {
        $params = [
            'QueueUrl' => $this->sqsQueueEndpoint,
            'ReceiptHandle' => $message->getReceiptHandle(),
        ];

        try {
            $result = $this->sqsClient->deleteMessage($params);

            return isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] === Response::HTTP_OK;
        } catch (\Throwable $e) {
            $this->logger->critical('Remove message from queue error: ' . $e->getMessage(), [
                'request_data' => json_encode($params),
            ]);

            return false;
        }
    }

    /**
     * Get queue url by queue name.
     *
     * @param string $queueName
     * @return void
     * @throws \RuntimeException
     */
    private function validateQueueName(string $queueName)
    {
        if (!\in_array($queueName, SqsQueuesBag::PUBLIC_QUEUES, true)) {
            throw new \RuntimeException(sprintf(self::SQS_QUEUE_IS_NOT_DEFINED, $queueName));
        }
    }

    /**
     * Check whether queue Standard or FIFO.
     *
     * @return bool
     */
    private function isFifoQueue() : bool
    {
        return strrpos($this->sqsQueueEndpoint, '.fifo') !== false;
    }
}

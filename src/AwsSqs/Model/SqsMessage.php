<?php

namespace App\AwsSqs\Model;

/**
 * Class SqsMessage
 *
 * @package App\AwsSqs\Model
 */
class SqsMessage
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $receiptHandle;

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var int
     */
    private $sentTimestamp;

    /**
     * SqsMessage constructor.
     *
     * @param array $rawSqsMessage
     */
    public function __construct(array $rawSqsMessage)
    {
        $this->id = $rawSqsMessage['MessageId'];
        $this->body = $rawSqsMessage['Body'];
        $this->queueName = $rawSqsMessage['Attributes']['MessageGroupId'] ?? $rawSqsMessage['MessageAttributes']['QueueName']['StringValue'];
        $this->sentTimestamp = $rawSqsMessage['Attributes']['SentTimestamp'];
        $this->receiptHandle = $rawSqsMessage['ReceiptHandle'];
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody() : string
    {
        return $this->body;
    }

    /**
     * Get queue name
     *
     * @return string
     */
    public function getQueueName() : string
    {
        return $this->queueName;
    }

    /**
     * Get sent timestamp
     *
     * @return int
     */
    public function getSentTimestamp() : int
    {
        return $this->sentTimestamp;
    }

    /**
     * Get receipt handle
     *
     * @return string
     */
    public function getReceiptHandle() : string
    {
        return $this->receiptHandle;
    }
}

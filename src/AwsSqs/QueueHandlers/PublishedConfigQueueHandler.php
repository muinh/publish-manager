<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\ConfigService;

/**
 * Class PublishedConfigQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishedConfigQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * PublishedConfigQueueHandler constructor.
     *
     * @param ConfigService $configService
     */
    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(string $queueName) : bool
    {
        return SqsQueuesBag::PUBLISHED_CONFIG === $queueName;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $config = json_decode($message->getBody(), true);

        return $this->getConfigService()
            ->handlePublishedConfigEvent($config);
    }

    /**
     * Get configService
     *
     * @return ConfigService
     */
    protected function getConfigService() : ConfigService
    {
        return $this->configService;
    }
}
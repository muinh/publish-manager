<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\ConfigService;

/**
 * Class PublishConfigQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishConfigQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * PublishConfigQueueHandler construct.
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
        return SqsQueuesBag::PUBLISH_CONFIG === $queueName;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $config = json_decode($message->getBody(), true);

        return $this->getConfigService()->handlePublishConfigEvent($config);
    }

    /**
     * Get configService.
     *
     * @return ConfigService
     */
    protected function getConfigService() : ConfigService
    {
        return $this->configService;
    }
}

<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\AdsShowerService;

/**
 * Class PublishAdsShowerQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishAdsShowerQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var AdsShowerService
     */
    private $adsShowerService;

    /**
     * PublishAdsShowerQueueHandler constructor.
     *
     * @codeCoverageIgnore
     *
     * @param AdsShowerService $adsShowerService
     */
    public function __construct(AdsShowerService $adsShowerService)
    {
        $this->adsShowerService = $adsShowerService;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(string $queueName) : bool
    {
        return $queueName === SqsQueuesBag::PUBLISH_ADS_SHOWER;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $adsShowerData = json_decode($message->getBody(), true);

        return $this->adsShowerService->handlePublishAdsShowerEvent($adsShowerData);
    }
}

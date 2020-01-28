<?php

namespace App\AwsSqs\QueueHandlers;

use App\AwsSqs\Model\SqsMessage;
use App\AwsSqs\{SqsQueuesBag, SqsQueueHandlerInterface};
use App\Service\AdsShowerService;

/**
 * Class PublishedAdsShowerQueueHandler
 *
 * @package App\AwsSqs\QueueHandlers
 */
class PublishedAdsShowerQueueHandler implements SqsQueueHandlerInterface
{
    /**
     * @var AdsShowerService
     */
    private $adsShowerService;

    /**
     * PublishedAdsShowerQueueHandler constructor.
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
        return $queueName === SqsQueuesBag::PUBLISHED_ADS_SHOWER;
    }

    /**
     * {@inheritdoc}
     */
    public function handleMessage(SqsMessage $message) : bool
    {
        $adsShowerData = json_decode($message->getBody(), true);

        return $this->adsShowerService->handlePublishedAdsShowerEvent($adsShowerData);
    }
}

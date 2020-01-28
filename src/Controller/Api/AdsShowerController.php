<?php

namespace App\Controller\Api;

use App\Controller\AbstractBaseController;
use App\Service\AdsShowerService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdsShowerController
 *
 * @package App\Controller\Api
 */
class AdsShowerController extends AbstractBaseController
{
    /**
     * Handle ads shower publishing request.
     *
     * @Route("/publish/ads-shower/", methods={"POST"}, name="api_publish_ads_shower")
     *
     * @param AdsShowerService $adsShowerService
     * @return Response
     */
    public function publishAnalyticScriptAction(AdsShowerService $adsShowerService) : Response
    {
        return $this->handleResponse($adsShowerService->handlePublishAdsShowerRequest());
    }
}

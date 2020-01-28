<?php

namespace App\Controller\Api;

use App\Controller\AbstractBaseController;
use App\Service\AnalyticScriptService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AnalyticScriptController
 *
 * @package App\Controller\Api
 */
class AnalyticScriptController extends AbstractBaseController
{
    /**
     * Handle analytic script publishing request.
     *
     * @Route("/publish/analytic-script/", methods={"POST"}, name="api_publish_analytic_script")
     *
     * @param AnalyticScriptService $analyticScriptService
     * @return Response
     */
    public function publishAnalyticScriptAction(AnalyticScriptService $analyticScriptService) : Response
    {
        return $this->handleResponse($analyticScriptService->handlePublishAnalyticScriptRequest());
    }
}

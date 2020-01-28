<?php

namespace App\Controller\Api;

use App\Controller\AbstractBaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ConfigService;

/**
 * Class ConfigController
 *
 * @package App\Controller\Api
 */
class ConfigController extends AbstractBaseController
{
    /**
     * Handle config publishing request.
     *
     * @Route("/publish/config/", methods={"POST"}, name="api_publish_config")
     *
     * @param ConfigService $configService
     * @return Response
     */
    public function publishConfigAction(ConfigService $configService) : Response
    {
        return $this->handleResponse($configService->handlePublishConfigRequest());
    }
}

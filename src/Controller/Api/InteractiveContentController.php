<?php

namespace App\Controller\Api;

use App\Controller\AbstractBaseController;
use App\Service\InteractiveContentService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InteractiveContentController
 *
 * @package App\Controller\Api
 */
class InteractiveContentController extends AbstractBaseController
{
    /**
     * Handle interactive content publishing request.
     *
     * @Route("/publish/interactive-content/", methods={"POST"}, name="api_publish_interactive_content")
     *
     * @param InteractiveContentService $interactiveContentService
     * @return Response
     */
    public function publishPostAction(InteractiveContentService $interactiveContentService) : Response
    {
        return $this->handleResponse($interactiveContentService->handlePublishRequest());
    }
}

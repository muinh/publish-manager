<?php

namespace App\Controller\Api;

use App\Controller\AbstractBaseController;
use App\Service\SeoTagService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SeoTagController
 *
 * @package App\Controller\Api
 */
class SeoTagController extends AbstractBaseController
{
    /**
     * Handle publishing request.
     *
     * @Route("/publish/seo-tags/", methods={"POST"}, name="api_publish_seo_tags")
     *
     * @param SeoTagService $seoTagService
     * @return Response
     */
    public function publishAction(SeoTagService $seoTagService) : Response
    {
        return $this->handleResponse($seoTagService->handlePublishSeoTagsRequest());
    }
}

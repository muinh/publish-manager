<?php

namespace App\Controller\Api;

use App\Controller\AbstractBaseController;
use App\Service\SeoTagsGroupsService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SeoTagsGroupController
 *
 * @package App\Controller\Api
 */
class SeoTagsGroupController extends AbstractBaseController
{
    /**
     * Handle publishing request.
     *
     * @Route("/publish/seo-tags-groups/", methods={"POST"}, name="api_publish_seo_tags_groups")
     *
     * @param SeoTagsGroupsService $seoTagsGroupsService
     * @return Response
     */
    public function publishAction(SeoTagsGroupsService $seoTagsGroupsService) : Response
    {
        return $this->handleResponse($seoTagsGroupsService->handlePublishSeoTagsGroupsRequest());
    }
}

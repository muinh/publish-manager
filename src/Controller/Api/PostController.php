<?php

namespace App\Controller\Api;

use App\Controller\AbstractBaseController;
use App\Service\PostService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PostController
 *
 * @package App\Controller\Api
 */
class PostController extends AbstractBaseController
{
    /**
     * Handle post publishing request.
     *
     * @Route("/publish/post/", methods={"POST"}, name="api_publish_post")
     *
     * @param PostService $postService
     * @return Response
     */
    public function publishPostAction(PostService $postService) : Response
    {
        return $this->handleResponse($postService->handlePublishPostRequest());
    }

    /**
     * Handle post unpublish request.
     *
     * @Route("/unpublish/post/", methods={"POST"}, name="api_unpublish_post")
     *
     * @param PostService $postService
     * @return Response
     */
    public function unpublishPostAction(PostService $postService) : Response
    {
        return $this->handleResponse($postService->handleUnpublishPostRequest());
    }
}

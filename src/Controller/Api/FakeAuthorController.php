<?php

namespace App\Controller\Api;

use App\Controller\AbstractBaseController;
use App\Service\FakeAuthorService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FakeAuthorController
 *
 * @package App\Controller\Api
 */
class FakeAuthorController extends AbstractBaseController
{
    /**
     * Handle categories publishing request.
     *
     * @Route("/publish/fake-author/", methods={"POST"}, name="api_publish_fake_author")
     *
     * @param FakeAuthorService $fakeAuthorService
     * @return Response
     */
    public function publishCategoriesAction(FakeAuthorService $fakeAuthorService) : Response
    {
        return $this->handleResponse($fakeAuthorService->handlePublishFakeAuthorRequest());
    }
}
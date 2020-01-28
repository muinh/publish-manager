<?php

namespace App\Controller\Api;

use App\Controller\AbstractBaseController;
use App\Service\CategoryService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryController
 *
 * @package App\Controller\Api
 */
class CategoryController extends AbstractBaseController
{
    /**
     * Handle categories publishing request.
     *
     * @Route("/publish/categories/", methods={"POST"}, name="api_publish_categories")
     *
     * @param CategoryService $categoryService
     * @return Response
     */
    public function publishCategoriesAction(CategoryService $categoryService) : Response
    {
        return $this->handleResponse($categoryService->handlePublishCategoryRequest());
    }
}

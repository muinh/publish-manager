<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class RootController
 *
 * @package App\Controller
 */
class RootController extends AbstractController
{
    /**
     * Default empty page for a root rout.
     *
     * @Route("/", name="root_route")
     *
     * @return JsonResponse
     */
    public function indexAction() : JsonResponse
    {
        return $this->json('OK');
    }
}

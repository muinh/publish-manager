<?php

namespace App\Controller;

use App\Model\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseController
 *
 * @package App\Controller
 */
abstract class AbstractBaseController extends AbstractController
{
    /**
     * Handle base api response.
     *
     * @param ApiResponse $response
     * @return Response
     */
    public function handleResponse(ApiResponse $response) : Response
    {
        return $this->json($response->getContent(), $response->getStatusCode(), $response->getHeaders());
    }
}

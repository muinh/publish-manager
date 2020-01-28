<?php

namespace App\Model;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiResponse
 *
 * @package App\Model
 */
class ApiResponse
{
    /**
     * @var mixed
     */
    private $content = 'Success!';

    /**
     * @var int
     */
    private $statusCode = Response::HTTP_OK;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * Get content.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content.
     *
     * @param mixed $content
     * @return ApiResponse
     */
    public function setContent($content) : ApiResponse
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get statusCode.
     *
     * @return int
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    /**
     * Set statusCode.
     *
     * @param int $statusCode
     * @return ApiResponse
     */
    public function setStatusCode(int $statusCode) : ApiResponse
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Get headers.
     *
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Set headers.
     *
     * @param array $headers
     * @return ApiResponse
     */
    public function setHeaders(array $headers) : ApiResponse
    {
        $this->headers = $headers;

        return $this;
    }
}

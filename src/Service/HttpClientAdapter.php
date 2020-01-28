<?php

namespace App\Service;

use GuzzleHttp\{ClientInterface, RequestOptions};
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class HttpClientAdapter
 *
 * @package App\Service
 */
class HttpClientAdapter
{
    private const AUTH_TOKEN_HEADER = 'X-AUTH-TOKEN';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $cmsAdminApiToken;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * HttpClientAdapter constructor.
     *
     * @codeCoverageIgnore
     *
     * @param ClientInterface $client
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     * @param string $cmsAdminApiToken
     */
    public function __construct(
        ClientInterface $client,
        SerializerInterface $serializer,
        string $cmsAdminApiToken,
        LoggerInterface $logger
    ) {
        $this->httpClient = $client;
        $this->serializer = $serializer;
        $this->cmsAdminApiToken = $cmsAdminApiToken;
        $this->logger = $logger;
    }

    /**
     * Post request.
     *
     * @param string $uri
     * @param mixed $requestData
     * @return ResponseInterface
     */
    public function post(string $uri, $requestData = []) : ResponseInterface
    {
        $serializedRequestData = $this->serializer->serialize($requestData, 'json');

        return $this->handleRequest('post', $uri, [
            RequestOptions::BODY => $serializedRequestData,
        ]);
    }

    /**
     * Get response from request.
     *
     * Guzzle client throws exception if status code not equals 200 OK, so we need to handle it at each request
     * Method writes detailed log about each request
     *
     * @param string $method
     * @param string $uri
     * @param mixed $data
     * @return ResponseInterface
     */
    private function handleRequest(string $method, string $uri, $data) : ResponseInterface
    {
        try {
            $dataToSend = array_merge([
                RequestOptions::HEADERS => [
                    self::AUTH_TOKEN_HEADER => $this->cmsAdminApiToken
                ]
            ], $data);

            $response = $this->httpClient->$method($uri, $dataToSend);
        } catch (RequestException $e) {
            $response = $e->getResponse();

            if ($response === null) {
                $response = new Response(HttpResponse::HTTP_BAD_REQUEST, [], $e->getMessage());
            }
        } catch (\Throwable $e) {
            $response = new Response($e->getCode(), [], $e->getMessage());
        }

        $this->logger->info('Http request executed.', [
            'request_uri' => $uri,
            'request_data' => $data,
            'response_content' => $response->getBody()->getContents(),
            'response_status_code' => $response->getStatusCode(),
        ]);

        return $response;
    }
}

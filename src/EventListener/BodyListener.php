<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\{ParameterBag, Request};
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException, UnsupportedMediaTypeHttpException};
use Symfony\Component\Serializer\Encoder\{DecoderInterface, JsonEncoder};
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Class BodyListener.
 *
 * To convert each json request to $request params
 *
 * @package App\EventListener
 */
class BodyListener
{
    /**
     * Default format.
     */
    private const DEFAULT_FORMAT = JsonEncoder::FORMAT;

    /**
     * List of supported request methods.
     */
    private const SUPPORTED_REQUEST_METHODS = [
        Request::METHOD_POST,
        Request::METHOD_PUT,
        Request::METHOD_PATCH,
        Request::METHOD_DELETE
    ];

    /**
     * Decoder provider.
     *
     * @var DecoderInterface $decoderProvider
     */
    private $decoderProvider;

    /**
     * BodyListener constructor.
     *
     * @codeCoverageIgnore
     *
     * @param DecoderInterface $decoderProvider
     */
    public function __construct(DecoderInterface $decoderProvider)
    {
        $this->decoderProvider = $decoderProvider;
    }

    /**
     * Core request handler.
     *
     * @param GetResponseEvent $event
     * @throws BadRequestHttpException
     * @throws UnsupportedMediaTypeHttpException
     * @throws \LogicException
     * @throws UnexpectedValueException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $contentType = $request->headers->get('Content-Type');

        if ($this->canDecode($request)) {
            $format = ($contentType === null) ? $request->getRequestFormat() : $request->getFormat($contentType);
            $format = $format ?: self::DEFAULT_FORMAT;

            $content = $request->getContent();

            if (!$this->decoderProvider->supportsDecoding($format)) {
                throw new UnsupportedMediaTypeHttpException("Request body format '{$format}' not supported");
            }

            if ($content) {
                $data = $this->decoderProvider->decode($content, $format);

                if (is_array($data)) {
                    $request->request = new ParameterBag($data);
                } else {
                    throw new BadRequestHttpException("Invalid '{$format}' message received");
                }
            }
        }
    }

    /**
     * Check if we should try to decode the body.
     *
     * @param Request $request
     * @return bool
     */
    private function canDecode(Request $request) : bool
    {
        return in_array($request->getMethod(), self::SUPPORTED_REQUEST_METHODS, true) && !$this->isFormRequest($request);
    }

    /**
     * Check if the content type indicates a form submission.
     *
     * @param Request $request
     * @return bool
     */
    private function isFormRequest(Request $request) : bool
    {
        $contentTypeParts = explode(';', $request->headers->get('Content-Type'));

        return in_array($contentTypeParts[0] ?? null, ['multipart/form-data', 'application/x-www-form-urlencoded'], true);
    }
}
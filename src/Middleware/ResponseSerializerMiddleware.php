<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Middleware;

use FactorioItemBrowser\ExportQueue\Server\Response\ClientResponse;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The middleware serializing the response.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ResponseSerializerMiddleware implements MiddlewareInterface
{
    /**
     * The serializer.
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Initializes the middleware.
     * @param SerializerInterface $exportQueueClientSerializer
     */
    public function __construct(SerializerInterface $exportQueueClientSerializer)
    {
        $this->serializer = $exportQueueClientSerializer;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($response instanceof ClientResponse) {
            $serializedResponse = $this->serializer->serialize($response->getResponse(), 'json');
            $response = $response->withSerializedResponse($serializedResponse);
        }
        return $response;
    }
}

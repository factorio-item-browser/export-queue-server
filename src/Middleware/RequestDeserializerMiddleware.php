<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Middleware;

use FactorioItemBrowser\ExportQueue\Client\Constant\ParameterName;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\ListRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\RequestInterface;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Router\RouteResult;

/**
 * The middleware for deserializing the request.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RequestDeserializerMiddleware implements MiddlewareInterface
{
    /**
     * The serializer.
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * The request classes by their corresponding routes.
     * @var array|string[]
     */
    protected $requestClassesByRoutes;

    /**
     * RequestDeserializerMiddleware constructor.
     * @param SerializerInterface $exportQueueClientSerializer
     * @param array|string[] $requestClassesByRoutes
     */
    public function __construct(SerializerInterface $exportQueueClientSerializer, array $requestClassesByRoutes)
    {
        $this->serializer = $exportQueueClientSerializer;
        $this->requestClassesByRoutes = $requestClassesByRoutes;
    }

    /**
     * Process an incoming server request.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /* @var RouteResult $route */
        $route = $request->getAttribute(RouteResult::class);
        $requestClass = $this->requestClassesByRoutes[$route->getMatchedRouteName()];

        $clientRequest = $this->deserializeBody($request, $requestClass);
        $this->parseRouteParameters($request, $clientRequest);
        $this->parseQueryParameters($request, $clientRequest);

        $request = $request->withAttribute(RequestInterface::class, $clientRequest);
        return $handler->handle($request);
    }

    /**
     * Deserializes the request body into a client request instance.
     * @param ServerRequestInterface $request
     * @param string $requestClass
     * @return RequestInterface
     */
    protected function deserializeBody(ServerRequestInterface $request, string $requestClass): RequestInterface
    {
        $content = $request->getBody()->getContents();
        if ($content === '') {
            $content = '{}';
        }
        // @todo Error handling
        return $this->serializer->deserialize($content, $requestClass, 'json');
    }

    /**
     * Parses the route parameters for the client request.
     * @param ServerRequestInterface $request
     * @param RequestInterface $clientRequest
     */
    protected function parseRouteParameters(ServerRequestInterface $request, RequestInterface $clientRequest): void
    {
        $callback = [$clientRequest, 'setJobId'];
        if (is_callable($callback)) {
            call_user_func($callback, (int) $request->getAttribute('job-id'));
        }
    }

    /**
     * Parses the query parameters for the client request.
     * @param ServerRequestInterface $request
     * @param RequestInterface $clientRequest
     */
    protected function parseQueryParameters(ServerRequestInterface $request, RequestInterface $clientRequest): void
    {
        if ($clientRequest instanceof ListRequest) {
            $queryParams = $request->getQueryParams();

            $clientRequest->setCombinationHash($queryParams[ParameterName::COMBINATION_HASH] ?? '')
                          ->setStatus($queryParams[ParameterName::STATUS] ?? '')
                          ->setLimit(((int) $queryParams[ParameterName::LIMIT]) ?? 0);
        }
    }
}

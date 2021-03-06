<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Middleware;

use Exception;
use FactorioItemBrowser\ExportQueue\Client\Constant\ListOrder;
use FactorioItemBrowser\ExportQueue\Client\Constant\ParameterName;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\ListRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\RequestInterface;
use FactorioItemBrowser\ExportQueue\Server\Exception\ExportQueueServerException;
use FactorioItemBrowser\ExportQueue\Server\Exception\MalformedRequestException;
use JMS\Serializer\SerializerInterface;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
     * @var array<class-string<RequestInterface>>
     */
    protected $requestClassesByRoutes;

    /**
     * RequestDeserializerMiddleware constructor.
     * @param SerializerInterface $exportQueueClientSerializer
     * @param array<class-string<RequestInterface>> $requestClassesByRoutes
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
     * @throws ExportQueueServerException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /* @var RouteResult $route */
        $route = $request->getAttribute(RouteResult::class);
        $requestClass = $this->requestClassesByRoutes[$route->getMatchedRouteName()] ?? '';

        if ($requestClass !== '') {
            $clientRequest = $this->deserializeBody($request, $requestClass);
            $this->parseRouteParameters($request, $clientRequest);
            $this->parseQueryParameters($request, $clientRequest);

            $request = $request->withAttribute(RequestInterface::class, $clientRequest);
        }
        return $handler->handle($request);
    }

    /**
     * Deserializes the request body into a client request instance.
     * @param ServerRequestInterface $request
     * @param class-string<RequestInterface> $requestClass
     * @return RequestInterface
     * @throws ExportQueueServerException
     */
    protected function deserializeBody(ServerRequestInterface $request, $requestClass): RequestInterface
    {
        $content = $request->getBody()->getContents();
        if ($content === '') {
            $content = '{}';
        }

        try {
            return $this->serializer->deserialize($content, $requestClass, 'json');
        } catch (Exception $e) {
            throw new MalformedRequestException($e->getMessage(), $e);
        }
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
            call_user_func($callback, $request->getAttribute('job-id', ''));
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

            $clientRequest->setCombinationId($queryParams[ParameterName::COMBINATION_ID] ?? '')
                          ->setStatus($queryParams[ParameterName::STATUS] ?? '')
                          ->setOrder($queryParams[ParameterName::ORDER] ?? ListOrder::CREATION_TIME)
                          ->setLimit(((int) $queryParams[ParameterName::LIMIT]) ?? 0);
        }
    }
}

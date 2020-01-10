<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Middleware;

use BluePsyduck\TestHelper\ReflectionTrait;
use Exception;
use FactorioItemBrowser\ExportQueue\Client\Constant\ParameterName;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\DetailsRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\ListRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\RequestInterface;
use FactorioItemBrowser\ExportQueue\Server\Exception\ExportQueueServerException;
use FactorioItemBrowser\ExportQueue\Server\Exception\MalformedRequestException;
use FactorioItemBrowser\ExportQueue\Server\Middleware\RequestDeserializerMiddleware;
use JMS\Serializer\SerializerInterface;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;

/**
 * The PHPUnit test of the RequestDeserializerMiddleware class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Middleware\RequestDeserializerMiddleware
 */
class RequestDeserializerMiddlewareTest extends TestCase
{
    use ReflectionTrait;

    /**
     * The mocked serializer.
     * @var SerializerInterface&MockObject
     */
    protected $serializer;

    /**
     * Sets up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $requestClassesByRoutes = ['abc' => RequestInterface::class];

        $middleware = new RequestDeserializerMiddleware($this->serializer, $requestClassesByRoutes);

        $this->assertSame($this->serializer, $this->extractProperty($middleware, 'serializer'));
        $this->assertSame($requestClassesByRoutes, $this->extractProperty($middleware, 'requestClassesByRoutes'));
    }

    /**
     * Tests the process method.
     * @throws ExportQueueServerException
     * @covers ::process
     */
    public function testProcess(): void
    {
        $matchesRouteName = 'abc';
        $requestClass = 'def';
        $requestClassesByRoutes = [
            'abc' => 'def',
        ];

        /* @var ServerRequestInterface&MockObject $request2 */
        $request2 = $this->createMock(ServerRequestInterface::class);
        /* @var ResponseInterface&MockObject $response */
        $response = $this->createMock(ResponseInterface::class);
        /* @var RequestInterface&MockObject $clientRequest */
        $clientRequest = $this->createMock(RequestInterface::class);

        /* @var RouteResult&MockObject $routeResult */
        $routeResult = $this->createMock(RouteResult::class);
        $routeResult->expects($this->once())
                    ->method('getMatchedRouteName')
                    ->willReturn($matchesRouteName);

        /* @var ServerRequestInterface&MockObject $request1 */
        $request1 = $this->createMock(ServerRequestInterface::class);
        $request1->expects($this->once())
                 ->method('getAttribute')
                 ->with($this->identicalTo(RouteResult::class))
                 ->willReturn($routeResult);
        $request1->expects($this->once())
                 ->method('withAttribute')
                 ->with($this->identicalTo(RequestInterface::class), $this->identicalTo($clientRequest))
                 ->willReturn($request2);

        /* @var RequestHandlerInterface&MockObject $handler */
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
                ->method('handle')
                ->with($this->identicalTo($request2))
                ->willReturn($response);

        /* @var RequestDeserializerMiddleware&MockObject $middleware */
        $middleware = $this->getMockBuilder(RequestDeserializerMiddleware::class)
                           ->onlyMethods(['deserializeBody', 'parseRouteParameters', 'parseQueryParameters'])
                           ->setConstructorArgs([$this->serializer, $requestClassesByRoutes])
                           ->getMock();
        $middleware->expects($this->once())
                   ->method('deserializeBody')
                   ->with($this->identicalTo($request1), $this->identicalTo($requestClass))
                   ->willReturn($clientRequest);
        $middleware->expects($this->once())
                   ->method('parseRouteParameters')
                   ->with($this->identicalTo($request1), $this->identicalTo($clientRequest));
        $middleware->expects($this->once())
                   ->method('parseQueryParameters')
                   ->with($this->identicalTo($request1), $this->identicalTo($clientRequest));

        $result = $middleware->process($request1, $handler);

        $this->assertSame($response, $result);
    }

    /**
     * Tests the process method.
     * @throws ExportQueueServerException
     * @covers ::process
     */
    public function testProcessWithoutClientRequest(): void
    {
        $matchesRouteName = 'foo';
        $requestClassesByRoutes = [
            'abc' => 'def',
        ];

        /* @var ResponseInterface&MockObject $response */
        $response = $this->createMock(ResponseInterface::class);

        /* @var RouteResult&MockObject $routeResult */
        $routeResult = $this->createMock(RouteResult::class);
        $routeResult->expects($this->once())
                    ->method('getMatchedRouteName')
                    ->willReturn($matchesRouteName);

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getAttribute')
                ->with($this->identicalTo(RouteResult::class))
                ->willReturn($routeResult);
        $request->expects($this->never())
                ->method('withAttribute');

        /* @var RequestHandlerInterface&MockObject $handler */
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
                ->method('handle')
                ->with($this->identicalTo($request))
                ->willReturn($response);

        /* @var RequestDeserializerMiddleware&MockObject $middleware */
        $middleware = $this->getMockBuilder(RequestDeserializerMiddleware::class)
                           ->onlyMethods(['deserializeBody', 'parseRouteParameters', 'parseQueryParameters'])
                           ->setConstructorArgs([$this->serializer, $requestClassesByRoutes])
                           ->getMock();
        $middleware->expects($this->never())
                   ->method('deserializeBody');
        $middleware->expects($this->never())
                   ->method('parseRouteParameters');
        $middleware->expects($this->never())
                   ->method('parseQueryParameters');

        $result = $middleware->process($request, $handler);

        $this->assertSame($response, $result);
    }

    /**
     * Tests the deserializeBody method.
     * @throws ReflectionException
     * @covers ::deserializeBody
     */
    public function testDeserializeBody(): void
    {
        $requestClass = 'abc';
        $content = 'def';

        /* @var RequestInterface&MockObject $clientRequest */
        $clientRequest = $this->createMock(RequestInterface::class);

        /* @var StreamInterface&MockObject $requestBody */
        $requestBody = $this->createMock(StreamInterface::class);
        $requestBody->expects($this->once())
                    ->method('getContents')
                    ->willReturn($content);

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getBody')
                ->willReturn($requestBody);

        $this->serializer->expects($this->once())
                         ->method('deserialize')
                         ->with(
                             $this->identicalTo($content),
                             $this->identicalTo($requestClass),
                             $this->identicalTo('json')
                         )
                         ->willReturn($clientRequest);

        $middleware = new RequestDeserializerMiddleware($this->serializer, []);
        $result = $this->invokeMethod($middleware, 'deserializeBody', $request, $requestClass);

        $this->assertSame($clientRequest, $result);
    }

    /**
     * Tests the deserializeBody method.
     * @throws ReflectionException
     * @covers ::deserializeBody
     */
    public function testDeserializeBodyWithoutContent(): void
    {
        $requestClass = 'abc';
        $content = '';
        $expectedContent = '{}';

        /* @var RequestInterface&MockObject $clientRequest */
        $clientRequest = $this->createMock(RequestInterface::class);

        /* @var StreamInterface&MockObject $requestBody */
        $requestBody = $this->createMock(StreamInterface::class);
        $requestBody->expects($this->once())
                    ->method('getContents')
                    ->willReturn($content);

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getBody')
                ->willReturn($requestBody);

        $this->serializer->expects($this->once())
                         ->method('deserialize')
                         ->with(
                             $this->identicalTo($expectedContent),
                             $this->identicalTo($requestClass),
                             $this->identicalTo('json')
                         )
                         ->willReturn($clientRequest);

        $middleware = new RequestDeserializerMiddleware($this->serializer, []);
        $result = $this->invokeMethod($middleware, 'deserializeBody', $request, $requestClass);

        $this->assertSame($clientRequest, $result);
    }

    /**
     * Tests the deserializeBody method.
     * @throws ReflectionException
     * @covers ::deserializeBody
     */
    public function testDeserializeBodyWithException(): void
    {
        $requestClass = 'abc';
        $content = 'def';

        /* @var StreamInterface&MockObject $requestBody */
        $requestBody = $this->createMock(StreamInterface::class);
        $requestBody->expects($this->once())
                    ->method('getContents')
                    ->willReturn($content);

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getBody')
                ->willReturn($requestBody);

        $this->serializer->expects($this->once())
                         ->method('deserialize')
                         ->with(
                             $this->identicalTo($content),
                             $this->identicalTo($requestClass),
                             $this->identicalTo('json')
                         )
                         ->willThrowException($this->createMock(Exception::class));

        $this->expectException(MalformedRequestException::class);

        $middleware = new RequestDeserializerMiddleware($this->serializer, []);
        $this->invokeMethod($middleware, 'deserializeBody', $request, $requestClass);
    }

    /**
     * Tests the parseRouteParameters method.
     * @throws ReflectionException
     * @covers ::parseRouteParameters
     */
    public function testParseRouteParameters(): void
    {
        $jobId = 'abc';

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getAttribute')
                ->with($this->identicalTo('job-id'), $this->identicalTo(''))
                ->willReturn($jobId);

        /* @var DetailsRequest&MockObject $clientRequest */
        $clientRequest = $this->createMock(DetailsRequest::class);
        $clientRequest->expects($this->once())
                       ->method('setJobId')
                       ->with($this->identicalTo($jobId));

        $middleware = new RequestDeserializerMiddleware($this->serializer, []);
        $this->invokeMethod($middleware, 'parseRouteParameters', $request, $clientRequest);
    }

    /**
     * Tests the parseRouteParameters method.
     * @throws ReflectionException
     * @covers ::parseRouteParameters
     */
    public function testParseRouteParametersWithoutJobId(): void
    {
        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->never())
                ->method('getAttribute');

        /* @var DetailsRequest&MockObject $clientRequest */
        $clientRequest = $this->createMock(RequestInterface::class);

        $middleware = new RequestDeserializerMiddleware($this->serializer, []);
        $this->invokeMethod($middleware, 'parseRouteParameters', $request, $clientRequest);
    }

    /**
     * Tests the parseQueryParameters method.
     * @throws ReflectionException
     * @covers ::parseQueryParameters
     */
    public function testParseQueryParameters(): void
    {
        $combinationId = 'abc';
        $status = 'def';
        $limit = 42;
        $queryParams = [
            ParameterName::COMBINATION_ID => 'abc',
            ParameterName::STATUS => 'def',
            ParameterName::LIMIT => '42',
        ];

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getQueryParams')
                ->willReturn($queryParams);

        /* @var ListRequest&MockObject $clientRequest */
        $clientRequest = $this->createMock(ListRequest::class);
        $clientRequest->expects($this->once())
                      ->method('setCombinationId')
                      ->with($this->identicalTo($combinationId))
                      ->willReturnSelf();
        $clientRequest->expects($this->once())
                      ->method('setStatus')
                      ->with($this->identicalTo($status))
                      ->willReturnSelf();
        $clientRequest->expects($this->once())
                      ->method('setLimit')
                      ->with($this->identicalTo($limit))
                      ->willReturnSelf();

        $middleware = new RequestDeserializerMiddleware($this->serializer, []);
        $this->invokeMethod($middleware, 'parseQueryParameters', $request, $clientRequest);
    }

    /**
     * Tests the parseQueryParameters method.
     * @throws ReflectionException
     * @covers ::parseQueryParameters
     */
    public function testParseQueryParametersWithoutListRequest(): void
    {
        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->never())
                ->method('getQueryParams');

        /* @var RequestInterface&MockObject $clientRequest */
        $clientRequest = $this->createMock(RequestInterface::class);

        $middleware = new RequestDeserializerMiddleware($this->serializer, []);
        $this->invokeMethod($middleware, 'parseQueryParameters', $request, $clientRequest);
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Middleware;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\ExportQueue\Server\Entity\Agent;
use FactorioItemBrowser\ExportQueue\Server\Exception\ExportQueueServerException;
use FactorioItemBrowser\ExportQueue\Server\Exception\InvalidAgentException;
use FactorioItemBrowser\ExportQueue\Server\Middleware\AgentMiddleware;
use FactorioItemBrowser\ExportQueue\Server\Repository\AgentRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;

/**
 * The PHPUnit test of the AgentMiddleware class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Middleware\AgentMiddleware
 */
class AgentMiddlewareTest extends TestCase
{
    use ReflectionTrait;

    /**
     * The mocked agent repository.
     * @var AgentRepository&MockObject
     */
    protected $agentRepository;

    /**
     * Sets up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->agentRepository = $this->createMock(AgentRepository::class);
    }

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $middleware = new AgentMiddleware($this->agentRepository);

        $this->assertSame($this->agentRepository, $this->extractProperty($middleware, 'agentRepository'));
    }

    /**
     * Tests the process method.
     * @throws ExportQueueServerException
     * @covers ::process
     */
    public function testProcess(): void
    {
        $accessKey = 'abc';

        /* @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        /* @var ResponseInterface&MockObject $response */
        $response = $this->createMock(ResponseInterface::class);
        /* @var ServerRequestInterface&MockObject $request2 */
        $request2 = $this->createMock(ServerRequestInterface::class);

        /* @var ServerRequestInterface&MockObject $request1 */
        $request1 = $this->createMock(ServerRequestInterface::class);
        $request1->expects($this->once())
                 ->method('getHeaderLine')
                 ->with($this->identicalTo('X-Api-Key'))
                 ->willReturn($accessKey);
        $request1->expects($this->once())
                 ->method('withAttribute')
                 ->with($this->identicalTo(Agent::class), $this->identicalTo($agent))
                 ->willReturn($request2);

        $this->agentRepository->expects($this->once())
                              ->method('getByAccessKey')
                              ->with($this->identicalTo($accessKey))
                              ->willReturn($agent);

        /* @var RequestHandlerInterface&MockObject $handler */
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
                ->method('handle')
                ->with($this->identicalTo($request2))
                ->willReturn($response);

        $middleware = new AgentMiddleware($this->agentRepository);
        $result = $middleware->process($request1, $handler);

        $this->assertSame($response, $result);
    }

    /**
     * Tests the process method.
     * @throws ExportQueueServerException
     * @covers ::process
     */
    public function testProcessWithException(): void
    {
        $accessKey = 'abc';

        /* @var ServerRequestInterface&MockObject $request1 */
        $request1 = $this->createMock(ServerRequestInterface::class);
        $request1->expects($this->once())
                 ->method('getHeaderLine')
                 ->with($this->identicalTo('X-Api-Key'))
                 ->willReturn($accessKey);
        $request1->expects($this->never())
                 ->method('withAttribute');

        $this->agentRepository->expects($this->once())
                              ->method('getByAccessKey')
                              ->with($this->identicalTo($accessKey))
                              ->willReturn(null);

        /* @var RequestHandlerInterface&MockObject $handler */
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())
                ->method('handle');

        $this->expectException(InvalidAgentException::class);

        $middleware = new AgentMiddleware($this->agentRepository);
        $middleware->process($request1, $handler);
    }
}

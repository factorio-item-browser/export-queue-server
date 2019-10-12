<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Middleware;

use FactorioItemBrowser\ExportQueue\Server\Entity\Agent;
use FactorioItemBrowser\ExportQueue\Server\Exception\ExportQueueServerException;
use FactorioItemBrowser\ExportQueue\Server\Exception\InvalidAgentException;
use FactorioItemBrowser\ExportQueue\Server\Repository\AgentRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The middleware authorizing the current agent.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class AgentMiddleware implements MiddlewareInterface
{
    /**
     * The agent repository.
     * @var AgentRepository
     */
    protected $agentRepository;

    /**
     * Initializes the middleware.
     * @param AgentRepository $agentRepository
     */
    public function __construct(AgentRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
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
        $accessKey = $request->getHeaderLine('X-Api-Key');
        $agent = $this->agentRepository->getByAccessKey($accessKey);
        if ($agent === null) {
            throw new InvalidAgentException();
        }

        $request = $request->withAttribute(Agent::class, $agent);
        return $handler->handle($request);
    }
}

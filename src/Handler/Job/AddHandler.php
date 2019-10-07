<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Handler\Job;

use FactorioItemBrowser\ExportQueue\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\ExportQueue\Server\Response\ClientResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The handler for adding a job to the export queue.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class AddHandler implements RequestHandlerInterface
{
    /**
     * Handles a request and produces a response.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new DetailsResponse();

        return new ClientResponse($response);
    }
}

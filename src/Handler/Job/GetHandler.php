<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Handler\Job;

use FactorioItemBrowser\ExportQueue\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job;
use FactorioItemBrowser\ExportQueue\Server\Response\ClientResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The handler for getting job details.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class GetHandler implements RequestHandlerInterface
{
    /**
     * Handles a request and produces a response.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $entity = new Job();

        $response = new DetailsResponse();
        $response->setId($entity->getId())
                 ->setCombinationHash($entity->getCombinationId()->toString())
                 ->setModNames($entity->getModNames())
                 ->setStatus($entity->getStatus())
                 ->setErrorMessage($entity->getErrorMessage())
                 ->setCreationTime($entity->getCreationTime())
                 ->setExportTime($entity->getExportTime())
                 ->setImportTime($entity->getImportTime());

        return new ClientResponse($response);
    }
}

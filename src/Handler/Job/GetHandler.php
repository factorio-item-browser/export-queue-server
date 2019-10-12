<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Handler\Job;

use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\DetailsRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\RequestInterface;
use FactorioItemBrowser\ExportQueue\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\ExportQueue\Server\Exception\ExportQueueServerException;
use FactorioItemBrowser\ExportQueue\Server\Exception\JobNotFoundException;
use FactorioItemBrowser\ExportQueue\Server\Repository\JobRepository;
use FactorioItemBrowser\ExportQueue\Server\Response\ClientResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

/**
 * The handler for getting job details.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class GetHandler implements RequestHandlerInterface
{
    /**
     * The job repository.
     * @var JobRepository
     */
    protected $jobRepository;

    /**
     * The mapper manager.
     * @var MapperManagerInterface
     */
    protected $mapperManager;

    /**
     * Initializes the handler.
     * @param JobRepository $jobRepository
     * @param MapperManagerInterface $mapperManager
     */
    public function __construct(JobRepository $jobRepository, MapperManagerInterface $mapperManager)
    {
        $this->jobRepository = $jobRepository;
        $this->mapperManager = $mapperManager;
    }

    /**
     * Handles a request and produces a response.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ExportQueueServerException
     * @throws MapperException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /* @var DetailsRequest $clientRequest */
        $clientRequest = $request->getAttribute(RequestInterface::class);
        $jobId = Uuid::fromString($clientRequest->getJobId());

        $entity = $this->jobRepository->findById($jobId);
        if ($entity === null) {
            throw new JobNotFoundException($jobId);
        }

        $response = new DetailsResponse();
        $this->mapperManager->map($entity, $response);
        return new ClientResponse($response);
    }
}

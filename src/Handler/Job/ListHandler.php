<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Handler\Job;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\ExportQueue\Client\Entity\Job as ClientJob;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\ListRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\RequestInterface;
use FactorioItemBrowser\ExportQueue\Client\Response\Job\ListResponse;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job as JobEntity;
use FactorioItemBrowser\ExportQueue\Server\Repository\JobRepository;
use FactorioItemBrowser\ExportQueue\Server\Response\ClientResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The handler for getting a list of jobs.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ListHandler implements RequestHandlerInterface
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
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /* @var ListRequest $clientRequest */
        $clientRequest = $request->getAttribute(RequestInterface::class);

        $jobs = $this->jobRepository->findAll(
            $clientRequest->getCombinationId(),
            $clientRequest->getStatus(),
            $clientRequest->getLimit()
        );

        $response = new ListResponse();
        $response->setJobs(array_map(function (JobEntity $job): ClientJob {
            $result = new ClientJob();
            $this->mapperManager->map($job, $result);
            return $result;
        }, $jobs));

        return new ClientResponse($response);
    }
}

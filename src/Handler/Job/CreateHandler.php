<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Handler\Job;

use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use DateTime;
use Exception;
use FactorioItemBrowser\ExportQueue\Client\Constant\JobStatus;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\CreateRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\RequestInterface;
use FactorioItemBrowser\ExportQueue\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\ExportQueue\Server\Entity\Agent;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job;
use FactorioItemBrowser\ExportQueue\Server\Exception\ActionNotAllowedException;
use FactorioItemBrowser\ExportQueue\Server\Repository\JobRepository;
use FactorioItemBrowser\ExportQueue\Server\Response\ClientResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

/**
 * The handler for creating a job in the export queue.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CreateHandler implements RequestHandlerInterface
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
     * @throws Exception
     * @throws MapperException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /* @var Agent $agent */
        $agent = $request->getAttribute(Agent::class);
        /* @var CreateRequest $clientRequest */
        $clientRequest = $request->getAttribute(RequestInterface::class);

        if (!$agent->getCanCreate()) {
            throw new ActionNotAllowedException('Creating new exports');
        }

        $job = $this->createJobEntity($clientRequest, $agent);
        $this->jobRepository->persist($job);

        $response = new DetailsResponse();
        $this->mapperManager->map($job, $response);
        return new ClientResponse($response);
    }

    /**
     * Creates a new job entity from the request.
     * @param CreateRequest $request
     * @param Agent $agent
     * @return Job
     * @throws Exception
     */
    protected function createJobEntity(CreateRequest $request, Agent $agent): Job
    {
        $job = new Job();
        $job->setCombinationId(Uuid::fromString($request->getCombinationId()))
            ->setModNames($request->getModNames())
            ->setStatus(JobStatus::QUEUED)
            ->setCreator($agent->getName())
            ->setCreationTime(new DateTime());
        return $job;
    }
}

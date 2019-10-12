<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Handler\Job;

use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use DateTime;
use Exception;
use FactorioItemBrowser\ExportQueue\Client\Constant\JobStatus;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\UpdateRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\RequestInterface;
use FactorioItemBrowser\ExportQueue\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\ExportQueue\Server\Entity\Agent;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job;
use FactorioItemBrowser\ExportQueue\Server\Exception\ActionNotAllowedException;
use FactorioItemBrowser\ExportQueue\Server\Exception\ExportQueueServerException;
use FactorioItemBrowser\ExportQueue\Server\Exception\InvalidStatusChangeException;
use FactorioItemBrowser\ExportQueue\Server\Exception\JobNotFoundException;
use FactorioItemBrowser\ExportQueue\Server\Repository\JobRepository;
use FactorioItemBrowser\ExportQueue\Server\Response\ClientResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

/**
 * The handler for updating a job.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class UpdateHandler implements RequestHandlerInterface
{
    /**
     * The allowed status changes.
     */
    protected const STATUS_CHANGES = [
        JobStatus::DOWNLOADING => JobStatus::QUEUED,
        JobStatus::PROCESSING  => JobStatus::DOWNLOADING,
        JobStatus::UPLOADING   => JobStatus::PROCESSING,
        JobStatus::UPLOADED    => JobStatus::UPLOADING,
        JobStatus::IMPORTING   => JobStatus::UPLOADED,
        JobStatus::DONE        => JobStatus::IMPORTING,
    ];

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
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /* @var Agent $agent */
        $agent = $request->getAttribute(Agent::class);
        /* @var UpdateRequest $clientRequest */
        $clientRequest = $request->getAttribute(RequestInterface::class);
        $jobId = Uuid::fromString($clientRequest->getJobId());

        $job = $this->jobRepository->findById($jobId);
        if ($job === null) {
            throw new JobNotFoundException($jobId);
        }

        $this->checkStatusChange($clientRequest, $job);
        $this->checkPermission($clientRequest, $agent);
        $this->changeJob($job, $clientRequest, $agent);

        $response = new DetailsResponse();
        $this->mapperManager->map($job, $response);
        return new ClientResponse($response);
    }

    /**
     * Checks whether the requested status change is valid.
     * @param UpdateRequest $request
     * @param Job $job
     * @throws InvalidStatusChangeException
     */
    protected function checkStatusChange(UpdateRequest $request, Job $job): void
    {
        if ($request->getStatus() === JobStatus::ERROR && $job->getStatus() !== JobStatus::ERROR) {
            // Changing to error is always possible, except the job already is error.
            return;
        }

        $requiredStatus = self::STATUS_CHANGES[$request->getStatus()] ?? '';
        if ($job->getStatus() === $requiredStatus) {
            return;
        }

        throw new InvalidStatusChangeException($job->getStatus(), $request->getStatus());
    }

    /**
     * Checks whether the permission is available to apply the requested update.
     * @param UpdateRequest $request
     * @param Agent $agent
     * @throws ExportQueueServerException
     */
    protected function checkPermission(UpdateRequest $request, Agent $agent): void
    {
        $hasPermission = true;
        switch ($request->getStatus()) {
            case JobStatus::DOWNLOADING:
            case JobStatus::PROCESSING:
            case JobStatus::UPLOADING:
            case JobStatus::UPLOADED:
                $hasPermission = $agent->getCanExport();
                break;

            case JobStatus::IMPORTING:
            case JobStatus::DONE:
                $hasPermission = $agent->getCanImport();
                break;

            case JobStatus::ERROR:
                $hasPermission = true;
                break;
        }

        if (!$hasPermission) {
            throw new ActionNotAllowedException('Changing status to ' . $request->getStatus());
        }
    }

    /**
     * Changes the job entity.
     * @param Job $job
     * @param UpdateRequest $request
     * @param Agent $agent
     * @throws Exception
     */
    protected function changeJob(Job $job, UpdateRequest $request, Agent $agent): void
    {
        $job->setStatus($request->getStatus())
            ->setErrorMessage($request->getErrorMessage());

        if ($request->getStatus() === JobStatus::DOWNLOADING) {
            $job->setExporter($agent->getName())
                ->setExportTime(new DateTime());
        } elseif ($request->getStatus() === JobStatus::IMPORTING) {
            $job->setImporter($agent->getName())
                ->setImportTime(new DateTime());
        }

        $this->jobRepository->persist($job);
    }
}

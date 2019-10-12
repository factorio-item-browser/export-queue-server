<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use FactorioItemBrowser\ExportQueue\Server\Doctrine\Type\JobStatus;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository of the Job entities.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class JobRepository
{
    /**
     * The entity manager.
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Initializes the repository.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Finds an export job by its id.
     * @param UuidInterface $jobId
     * @return Job|null
     */
    public function findById(UuidInterface $jobId): ?Job
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('j')
                     ->from(Job::class, 'j')
                     ->andWhere('j.id = :jobId')
                     ->setParameter('jobId', $jobId, UuidBinaryType::NAME);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // Should never happen, we are searching for the primary key here.
            return null;
        }
    }

    /**
     * Find all export jobs matching the specified criteria.
     * @param UuidInterface $combinationId
     * @param string $status
     * @param int $limit
     * @return array|Job[]
     */
    public function findAll(UuidInterface $combinationId, string $status, int $limit): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('j')
                     ->from(Job::class, 'j')
                     ->addOrderBy('j.creationTime', 'ASC')
                     ->setMaxResults($limit);

        if ($combinationId !== '') {
            $queryBuilder->andWhere('j.combinationId = :combinationId')
                         ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME);
        }
        if ($status !== '') {
            $queryBuilder->andWhere('j.status = :status')
                         ->setParameter('status', $status, JobStatus::NAME);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Persists an export job to the database.
     * @param Job $job
     */
    public function persist(Job $job): void
    {
        $this->entityManager->persist($job);
        $this->entityManager->flush();
    }
}

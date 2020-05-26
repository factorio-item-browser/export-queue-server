<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\ExportQueue\Client\Constant\JobPriority;
use FactorioItemBrowser\ExportQueue\Client\Constant\ListOrder;
use FactorioItemBrowser\ExportQueue\Server\Doctrine\Type\JobStatusType;
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
     * The values of the priorities for sorting.
     */
    protected const PRIORITIES = [
        JobPriority::ADMIN => 1,
        JobPriority::USER => 2,
        JobPriority::SCRIPT => 3,
    ];

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
     * @param UuidInterface|null $combinationId
     * @param string $status
     * @param string $order
     * @param int $limit
     * @return array|Job[]
     */
    public function findAll(?UuidInterface $combinationId, string $status, string $order, int $limit): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('j')
                     ->from(Job::class, 'j')
                     ->setMaxResults($limit);

        $this->addOrder($queryBuilder, $order);

        if ($combinationId !== null) {
            $queryBuilder->andWhere('j.combinationId = :combinationId')
                         ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME);
        }
        if ($status !== '') {
            $queryBuilder->andWhere('j.status = :status')
                         ->setParameter('status', $status, JobStatusType::NAME);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Adds the order clauses to the query builder.
     * @param QueryBuilder $queryBuilder
     * @param string $order
     */
    protected function addOrder(QueryBuilder $queryBuilder, string $order): void
    {
        switch ($order) {
            case ListOrder::PRIORITY:
                $conditions = [];
                foreach (self::PRIORITIES as $priority => $value) {
                    $conditions[] = "WHEN '{$priority}' THEN {$value}";
                }
                $queryBuilder->addSelect('CASE j.priority ' . implode(' ', $conditions) . ' ELSE 100 END AS HIDDEN p')
                             ->addOrderBy('p', 'ASC')
                             ->addOrderBy('j.creationTime', 'ASC');
                break;

            case ListOrder::LATEST:
                $queryBuilder->addOrderBy('j.creationTime', 'DESC');
                break;

            case ListOrder::CREATION_TIME:
            default:
                $queryBuilder->addOrderBy('j.creationTime', 'ASC');
                break;
        }
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

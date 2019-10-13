<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\ExportQueue\Server\Doctrine\Type\JobStatusType;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job;
use FactorioItemBrowser\ExportQueue\Server\Repository\JobRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * The PHPUnit test of the JobRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Repository\JobRepository
 */
class JobRepositoryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * The mocked entity manager.
     * @var EntityManagerInterface&MockObject
     */
    protected $entityManager;

    /**
     * Sets up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $repository = new JobRepository($this->entityManager);

        $this->assertSame($this->entityManager, $this->extractProperty($repository, 'entityManager'));
    }

    /**
     * Tests the findById method.
     * @covers ::findById
     */
    public function testFindById(): void
    {
        /* @var UuidInterface&MockObject $jobId */
        $jobId = $this->createMock(UuidInterface::class);
        /* @var Job&MockObject $job */
        $job = $this->createMock(Job::class);

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willReturn($job);

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('j'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Job::class), $this->identicalTo('j'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('j.id = :jobId'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with(
                         $this->identicalTo('jobId'),
                         $this->identicalTo($jobId),
                         $this->identicalTo(UuidBinaryType::NAME)
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new JobRepository($this->entityManager);
        $result = $repository->findById($jobId);

        $this->assertSame($job, $result);
    }

    /**
     * Tests the findById method.
     * @covers ::findById
     */
    public function testFindByIdWithImpossibleException(): void
    {
        /* @var UuidInterface&MockObject $jobId */
        $jobId = $this->createMock(UuidInterface::class);

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willThrowException($this->createMock(NonUniqueResultException::class));

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('j'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Job::class), $this->identicalTo('j'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('j.id = :jobId'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with(
                         $this->identicalTo('jobId'),
                         $this->identicalTo($jobId),
                         $this->identicalTo(UuidBinaryType::NAME)
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new JobRepository($this->entityManager);
        $result = $repository->findById($jobId);

        $this->assertNull($result);
    }

    /**
     * Tests the findAll method.
     * @covers ::findAll
     */
    public function testFindAll(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        $status = 'abc';
        $limit = 42;

        $jobs = [
            $this->createMock(Job::class),
            $this->createMock(Job::class),
        ];

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($jobs);

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('j'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Job::class), $this->identicalTo('j'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('addOrderBy')
                     ->with($this->identicalTo('j.creationTime'), $this->identicalTo('ASC'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setMaxResults')
                     ->with($this->identicalTo($limit))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('andWhere')
                     ->withConsecutive(
                         [$this->identicalTo('j.combinationId = :combinationId')],
                         [$this->identicalTo('j.status = :status')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME)
                         ],
                         [
                             $this->identicalTo('status'),
                             $this->identicalTo($status),
                             $this->identicalTo(JobStatusType::NAME)
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new JobRepository($this->entityManager);
        $result = $repository->findAll($combinationId, $status, $limit);

        $this->assertSame($jobs, $result);
    }

    /**
     * Tests the findAll method.
     * @covers ::findAll
     */
    public function testFindAllWithoutConditions(): void
    {
        $limit = 42;

        $jobs = [
            $this->createMock(Job::class),
            $this->createMock(Job::class),
        ];

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($jobs);

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('j'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Job::class), $this->identicalTo('j'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('addOrderBy')
                     ->with($this->identicalTo('j.creationTime'), $this->identicalTo('ASC'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setMaxResults')
                     ->with($this->identicalTo($limit))
                     ->willReturnSelf();
        $queryBuilder->expects($this->never())
                     ->method('andWhere');
        $queryBuilder->expects($this->never())
                     ->method('setParameter');
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new JobRepository($this->entityManager);
        $result = $repository->findAll(null, '', $limit);

        $this->assertSame($jobs, $result);
    }

    /**
     * Tests the persist method.
     * @covers ::persist
     */
    public function testPersist(): void
    {
        /* @var Job&MockObject $job */
        $job = $this->createMock(Job::class);

        $this->entityManager->expects($this->once())
                            ->method('persist')
                            ->with($this->identicalTo($job));
        $this->entityManager->expects($this->once())
                            ->method('flush');

        $repository = new JobRepository($this->entityManager);
        $repository->persist($job);
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Handler\Job;

use BluePsyduck\MapperManager\MapperManagerInterface;
use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\ExportQueue\Client\Entity\Job as ClientJob;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\ListRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\RequestInterface;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job;
use FactorioItemBrowser\ExportQueue\Server\Handler\Job\ListHandler;
use FactorioItemBrowser\ExportQueue\Server\Repository\JobRepository;
use FactorioItemBrowser\ExportQueue\Server\Response\ClientResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use ReflectionException;

/**
 * The PHPUnit test of the ListHandler class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Handler\Job\ListHandler
 */
class ListHandlerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * The mocked job repository.
     * @var JobRepository&MockObject
     */
    protected $jobRepository;

    /**
     * The mocked mapper manager.
     * @var MapperManagerInterface&MockObject
     */
    protected $mapperManager;

    /**
     * Sets up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->jobRepository = $this->createMock(JobRepository::class);
        $this->mapperManager = $this->createMock(MapperManagerInterface::class);
    }

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $handler = new ListHandler($this->jobRepository, $this->mapperManager);

        $this->assertSame($this->jobRepository, $this->extractProperty($handler, 'jobRepository'));
        $this->assertSame($this->mapperManager, $this->extractProperty($handler, 'mapperManager'));
    }

    /**
     * Tests the handle method.
     * @covers ::handle
     */
    public function testHandle(): void
    {
        $combinationIdString = '01234567-0123-0123-0123-0123456789ab';
        $combinationId = Uuid::fromString($combinationIdString);
        $status = 'abc';
        $limit = 42;

        /* @var Job&MockObject $job1 */
        $job1 = $this->createMock(Job::class);
        /* @var Job&MockObject $job2 */
        $job2 = $this->createMock(Job::class);

        /* @var ListRequest&MockObject $clientRequest */
        $clientRequest = $this->createMock(ListRequest::class);
        $clientRequest->expects($this->once())
                      ->method('getCombinationId')
                      ->willReturn($combinationIdString);
        $clientRequest->expects($this->once())
                      ->method('getStatus')
                      ->willReturn($status);
        $clientRequest->expects($this->once())
                      ->method('getLimit')
                      ->willReturn($limit);

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getAttribute')
                ->with($this->identicalTo(RequestInterface::class))
                ->willReturn($clientRequest);

        $this->jobRepository->expects($this->once())
                            ->method('findAll')
                            ->with(
                                $this->equalTo($combinationId),
                                $this->identicalTo($status),
                                $this->identicalTo($limit)
                            )
                            ->willReturn([$job1, $job2]);

        $this->mapperManager->expects($this->exactly(2))
                            ->method('map')
                            ->withConsecutive(
                                [$this->identicalTo($job1), $this->isInstanceOf(ClientJob::class)],
                                [$this->identicalTo($job2), $this->isInstanceOf(ClientJob::class)]
                            );

        $handler = new ListHandler($this->jobRepository, $this->mapperManager);
        $result = $handler->handle($request);

        $this->assertInstanceOf(ClientResponse::class, $result);
    }

    /**
     * Tests the handle method.
     * @covers ::handle
     */
    public function testHandleWithoutCombinationId(): void
    {
        $combinationIdString = '';
        $status = 'abc';
        $limit = 42;

        /* @var Job&MockObject $job1 */
        $job1 = $this->createMock(Job::class);
        /* @var Job&MockObject $job2 */
        $job2 = $this->createMock(Job::class);

        /* @var ListRequest&MockObject $clientRequest */
        $clientRequest = $this->createMock(ListRequest::class);
        $clientRequest->expects($this->once())
                      ->method('getCombinationId')
                      ->willReturn($combinationIdString);
        $clientRequest->expects($this->once())
                      ->method('getStatus')
                      ->willReturn($status);
        $clientRequest->expects($this->once())
                      ->method('getLimit')
                      ->willReturn($limit);

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getAttribute')
                ->with($this->identicalTo(RequestInterface::class))
                ->willReturn($clientRequest);

        $this->jobRepository->expects($this->once())
                            ->method('findAll')
                            ->with(
                                $this->isNull(),
                                $this->identicalTo($status),
                                $this->identicalTo($limit)
                            )
                            ->willReturn([$job1, $job2]);

        $this->mapperManager->expects($this->exactly(2))
                            ->method('map')
                            ->withConsecutive(
                                [$this->identicalTo($job1), $this->isInstanceOf(ClientJob::class)],
                                [$this->identicalTo($job2), $this->isInstanceOf(ClientJob::class)]
                            );

        $handler = new ListHandler($this->jobRepository, $this->mapperManager);
        $result = $handler->handle($request);

        $this->assertInstanceOf(ClientResponse::class, $result);
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Handler\Job;

use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use BluePsyduck\TestHelper\ReflectionTrait;
use Exception;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\DetailsRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\RequestInterface;
use FactorioItemBrowser\ExportQueue\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job;
use FactorioItemBrowser\ExportQueue\Server\Exception\JobNotFoundException;
use FactorioItemBrowser\ExportQueue\Server\Handler\Job\DetailsHandler;
use FactorioItemBrowser\ExportQueue\Server\Repository\JobRepository;
use FactorioItemBrowser\ExportQueue\Server\Response\ClientResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use ReflectionException;

/**
 * The PHPUnit test of the DetailsHandler class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Handler\Job\DetailsHandler
 */
class DetailsHandlerTest extends TestCase
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
        $handler = new DetailsHandler($this->jobRepository, $this->mapperManager);

        $this->assertSame($this->jobRepository, $this->extractProperty($handler, 'jobRepository'));
        $this->assertSame($this->mapperManager, $this->extractProperty($handler, 'mapperManager'));
    }

    /**
     * Tests the handle method.
     * @throws Exception
     * @throws MapperException
     * @covers ::handle
     */
    public function testHandle(): void
    {
        $jobIdString = '01234567-0123-0123-0123-0123456789ab';
        $jobId = Uuid::fromString($jobIdString);

        /* @var Job&MockObject $job */
        $job = $this->createMock(Job::class);

        /* @var DetailsRequest&MockObject $clientRequest */
        $clientRequest = $this->createMock(DetailsRequest::class);
        $clientRequest->expects($this->once())
                      ->method('getJobId')
                      ->willReturn($jobIdString);

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getAttribute')
                ->with($this->identicalTo(RequestInterface::class))
                ->willReturn($clientRequest);

        $this->jobRepository->expects($this->once())
                            ->method('findById')
                            ->with($this->equalTo($jobId))
                            ->willReturn($job);

        $this->mapperManager->expects($this->once())
                            ->method('map')
                            ->with($this->identicalTo($job), $this->isInstanceOf(DetailsResponse::class));

        $handler = new DetailsHandler($this->jobRepository, $this->mapperManager);
        $result = $handler->handle($request);

        $this->assertInstanceOf(ClientResponse::class, $result);
    }

    /**
     * Tests the handle method.
     * @throws Exception
     * @throws MapperException
     * @covers ::handle
     */
    public function testHandleWithoutEntity(): void
    {
        $jobIdString = '01234567-0123-0123-0123-0123456789ab';
        $jobId = Uuid::fromString($jobIdString);

        /* @var DetailsRequest&MockObject $clientRequest */
        $clientRequest = $this->createMock(DetailsRequest::class);
        $clientRequest->expects($this->once())
                      ->method('getJobId')
                      ->willReturn($jobIdString);

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
                ->method('getAttribute')
                ->with($this->identicalTo(RequestInterface::class))
                ->willReturn($clientRequest);

        $this->jobRepository->expects($this->once())
                            ->method('findById')
                            ->with($this->equalTo($jobId))
                            ->willReturn(null);

        $this->mapperManager->expects($this->never())
                            ->method('map');

        $this->expectException(JobNotFoundException::class);

        $handler = new DetailsHandler($this->jobRepository, $this->mapperManager);
        $handler->handle($request);
    }
}

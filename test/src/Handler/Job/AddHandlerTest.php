<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Handler\Job;

use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use BluePsyduck\TestHelper\ReflectionTrait;
use Exception;
use FactorioItemBrowser\ExportQueue\Client\Constant\JobStatus;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\CreateRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\RequestInterface;
use FactorioItemBrowser\ExportQueue\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\ExportQueue\Server\Entity\Agent;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job;
use FactorioItemBrowser\ExportQueue\Server\Exception\ActionNotAllowedException;
use FactorioItemBrowser\ExportQueue\Server\Handler\Job\AddHandler;
use FactorioItemBrowser\ExportQueue\Server\Repository\JobRepository;
use FactorioItemBrowser\ExportQueue\Server\Response\ClientResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use ReflectionException;

/**
 * The PHPUnit test of the AddHandler class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Handler\Job\AddHandler
 */
class AddHandlerTest extends TestCase
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
        $handler = new AddHandler($this->jobRepository, $this->mapperManager);

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
        /* @var CreateRequest&MockObject $clientRequest */
        $clientRequest = $this->createMock(CreateRequest::class);
        /* @var Job&MockObject $job */
        $job = $this->createMock(Job::class);

        /* @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->expects($this->once())
              ->method('getCanCreate')
              ->willReturn(true);

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->exactly(2))
                ->method('getAttribute')
                ->withConsecutive(
                    [$this->identicalTo(Agent::class)],
                    [$this->identicalTo(RequestInterface::class)]
                )
                ->willReturnOnConsecutiveCalls(
                    $agent,
                    $clientRequest
                );

        $this->jobRepository->expects($this->once())
                            ->method('persist')
                            ->with($this->identicalTo($job));

        $this->mapperManager->expects($this->once())
                            ->method('map')
                            ->with($this->identicalTo($job), $this->isInstanceOf(DetailsResponse::class));

        /* @var AddHandler&MockObject $handler */
        $handler = $this->getMockBuilder(AddHandler::class)
                        ->onlyMethods(['createJobEntity'])
                        ->setConstructorArgs([$this->jobRepository, $this->mapperManager])
                        ->getMock();
        $handler->expects($this->once())
                ->method('createJobEntity')
                ->with($this->identicalTo($clientRequest), $this->identicalTo($agent))
                ->willReturn($job);

        $result = $handler->handle($request);

        $this->assertInstanceOf(ClientResponse::class, $result);
    }

    /**
     * Tests the handle method.
     * @throws Exception
     * @throws MapperException
     * @covers ::handle
     */
    public function testHandleWithException(): void
    {
        /* @var CreateRequest&MockObject $clientRequest */
        $clientRequest = $this->createMock(CreateRequest::class);

        /* @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->expects($this->once())
              ->method('getCanCreate')
              ->willReturn(false);

        /* @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->exactly(2))
                ->method('getAttribute')
                ->withConsecutive(
                    [$this->identicalTo(Agent::class)],
                    [$this->identicalTo(RequestInterface::class)]
                )
                ->willReturnOnConsecutiveCalls(
                    $agent,
                    $clientRequest
                );

        $this->jobRepository->expects($this->never())
                            ->method('persist');

        $this->mapperManager->expects($this->never())
                            ->method('map');

        /* @var AddHandler&MockObject $handler */
        $handler = $this->getMockBuilder(AddHandler::class)
                        ->onlyMethods(['createJobEntity'])
                        ->setConstructorArgs([$this->jobRepository, $this->mapperManager])
                        ->getMock();
        $handler->expects($this->never())
                ->method('createJobEntity');

        $this->expectException(ActionNotAllowedException::class);

        $handler->handle($request);
    }

    /**
     * Tests the createJobEntity method.
     * @throws ReflectionException
     * @covers ::createJobEntity
     */
    public function testCreateJobEntity(): void
    {
        $combinationIdString = '01234567-0123-0123-0123-0123456789ab';
        $combinationId = Uuid::fromString($combinationIdString);
        $modNames = ['abc', 'def'];
        $agentName = 'ghi';

        /* @var CreateRequest&MockObject $request */
        $request = $this->createMock(CreateRequest::class);
        $request->expects($this->once())
                ->method('getCombinationId')
                ->willReturn($combinationIdString);
        $request->expects($this->once())
                ->method('getModNames')
                ->willReturn($modNames);

        /* @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->expects($this->once())
              ->method('getName')
              ->willReturn($agentName);

        $handler = new AddHandler($this->jobRepository, $this->mapperManager);
        $result = $this->invokeMethod($handler, 'createJobEntity', $request, $agent);

        /* @var Job $result */
        $this->assertEquals($combinationId, $result->getCombinationId());
        $this->assertSame($modNames, $result->getModNames());
        $this->assertSame(JobStatus::QUEUED, $result->getStatus());
        $this->assertSame($agentName, $result->getCreator());
        $this->assertNotNull($result->getCreationTime());
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Handler\Job;

use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use BluePsyduck\TestHelper\ReflectionTrait;
use DateTime;
use Exception;
use FactorioItemBrowser\ExportQueue\Client\Constant\JobStatus;
use FactorioItemBrowser\ExportQueue\Client\Request\Job\UpdateRequest;
use FactorioItemBrowser\ExportQueue\Client\Request\RequestInterface;
use FactorioItemBrowser\ExportQueue\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\ExportQueue\Server\Entity\Agent;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job;
use FactorioItemBrowser\ExportQueue\Server\Exception\ActionNotAllowedException;
use FactorioItemBrowser\ExportQueue\Server\Exception\InvalidStatusChangeException;
use FactorioItemBrowser\ExportQueue\Server\Exception\JobNotFoundException;
use FactorioItemBrowser\ExportQueue\Server\Handler\Job\UpdateHandler;
use FactorioItemBrowser\ExportQueue\Server\Repository\JobRepository;
use FactorioItemBrowser\ExportQueue\Server\Response\ClientResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use ReflectionException;

/**
 * The PHPUnit test of the UpdateHandler class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Handler\Job\UpdateHandler
 */
class UpdateHandlerTest extends TestCase
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
        $handler = new UpdateHandler($this->jobRepository, $this->mapperManager);

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
        /* @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);

        /* @var UpdateRequest&MockObject $clientRequest */
        $clientRequest = $this->createMock(UpdateRequest::class);
        $clientRequest->expects($this->once())
                      ->method('getJobId')
                      ->willReturn($jobIdString);

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
                            ->method('findById')
                            ->with($this->equalTo($jobId))
                            ->willReturn($job);

        $this->mapperManager->expects($this->once())
                            ->method('map')
                            ->with($this->identicalTo($job), $this->isInstanceOf(DetailsResponse::class));

        /* @var UpdateHandler&MockObject $handler */
        $handler = $this->getMockBuilder(UpdateHandler::class)
                        ->onlyMethods(['checkStatusChange', 'checkPermission', 'modifyJobEntity'])
                        ->setConstructorArgs([$this->jobRepository, $this->mapperManager])
                        ->getMock();
        $handler->expects($this->once())
                ->method('checkStatusChange')
                ->with($this->identicalTo($clientRequest), $this->identicalTo($job));
        $handler->expects($this->once())
                ->method('checkPermission')
                ->with($this->identicalTo($clientRequest), $this->identicalTo($agent));
        $handler->expects($this->once())
                ->method('modifyJobEntity')
                ->with($this->identicalTo($job), $this->identicalTo($clientRequest), $this->identicalTo($agent));

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

        /* @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);

        /* @var UpdateRequest&MockObject $clientRequest */
        $clientRequest = $this->createMock(UpdateRequest::class);
        $clientRequest->expects($this->once())
                      ->method('getJobId')
                      ->willReturn($jobIdString);

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
                            ->method('findById')
                            ->with($this->equalTo($jobId))
                            ->willReturn(null);

        $this->mapperManager->expects($this->never())
                            ->method('map');

        /* @var UpdateHandler&MockObject $handler */
        $handler = $this->getMockBuilder(UpdateHandler::class)
                        ->onlyMethods(['checkStatusChange', 'checkPermission', 'modifyJobEntity'])
                        ->setConstructorArgs([$this->jobRepository, $this->mapperManager])
                        ->getMock();
        $handler->expects($this->never())
                ->method('checkStatusChange');
        $handler->expects($this->never())
                ->method('checkPermission');
        $handler->expects($this->never())
                ->method('modifyJobEntity');

        $this->expectException(JobNotFoundException::class);

        $handler->handle($request);
    }

    /**
     * Provides the data for the checkStatusChange test.
     * @return array
     */
    public function provideCheckStatusChange(): array
    {
        return [
            // Intended status changes
            [JobStatus::QUEUED, JobStatus::DOWNLOADING, false],
            [JobStatus::DOWNLOADING, JobStatus::PROCESSING, false],
            [JobStatus::PROCESSING, JobStatus::UPLOADING, false],
            [JobStatus::UPLOADING, JobStatus::UPLOADED, false],
            [JobStatus::UPLOADED, JobStatus::IMPORTING, false],
            [JobStatus::IMPORTING, JobStatus::DONE, false],

            // Every status can change to error, except error itself.
            [JobStatus::QUEUED, JobStatus::ERROR, false],
            [JobStatus::DOWNLOADING, JobStatus::ERROR, false],
            [JobStatus::PROCESSING, JobStatus::ERROR, false],
            [JobStatus::UPLOADING, JobStatus::ERROR, false],
            [JobStatus::UPLOADED, JobStatus::ERROR, false],
            [JobStatus::IMPORTING, JobStatus::ERROR, false],
            [JobStatus::DONE, JobStatus::ERROR, false],
            [JobStatus::ERROR, JobStatus::ERROR, true],

            // Some invalid status changes
            [JobStatus::QUEUED, JobStatus::PROCESSING, true],
            [JobStatus::DOWNLOADING, JobStatus::UPLOADING, true],
            [JobStatus::PROCESSING, JobStatus::DONE, true],
            [JobStatus::UPLOADING, JobStatus::IMPORTING, true],
        ];
    }

    /**
     * Tests the checkStatusChange method.
     * @param string $currentStatus
     * @param string $newStatus
     * @param bool $expectException
     * @throws ReflectionException
     * @covers ::checkStatusChange
     * @dataProvider provideCheckStatusChange
     */
    public function testCheckStatusChange(string $currentStatus, string $newStatus, bool $expectException): void
    {
        /* @var Job&MockObject $job */
        $job = $this->createMock(Job::class);
        $job->expects($this->any())
            ->method('getStatus')
            ->willReturn($currentStatus);

        /* @var UpdateRequest&MockObject $request */
        $request = $this->createMock(UpdateRequest::class);
        $request->expects($this->any())
                ->method('getStatus')
                ->willReturn($newStatus);

        if ($expectException) {
            $this->expectException(InvalidStatusChangeException::class);
        } else {
            $this->addToAssertionCount(1);
        }

        $handler = new UpdateHandler($this->jobRepository, $this->mapperManager);
        $this->invokeMethod($handler, 'checkStatusChange', $request, $job);
    }

    /**
     * Provides the data for the checkPermission test.
     * @return array
     */
    public function provideCheckPermission(): array
    {
        return [
            [JobStatus::DOWNLOADING, true, false, false],
            [JobStatus::DOWNLOADING, false, true, true],
            [JobStatus::DOWNLOADING, false, false, true],
            [JobStatus::PROCESSING, true, false, false],
            [JobStatus::PROCESSING, false, true, true],
            [JobStatus::PROCESSING, false, false, true],
            [JobStatus::UPLOADING, true, false, false],
            [JobStatus::UPLOADING, false, true, true],
            [JobStatus::UPLOADING, false, false, true],
            [JobStatus::UPLOADED, true, false, false],
            [JobStatus::UPLOADED, false, true, true],
            [JobStatus::UPLOADED, false, false, true],

            [JobStatus::IMPORTING, false, true, false],
            [JobStatus::IMPORTING, true, false, true],
            [JobStatus::IMPORTING, false, false, true],
            [JobStatus::DONE, false, true, false],
            [JobStatus::DONE, true, false, true],
            [JobStatus::DONE, false, false, true],

            [JobStatus::ERROR, false, false, false],
        ];
    }

    /**
     * Tests the checkPermission method.
     * @param string $status
     * @param bool $canExport
     * @param bool $canImport
     * @param bool $expectException
     * @throws ReflectionException
     * @covers ::checkPermission
     * @dataProvider provideCheckPermission
     */
    public function testCheckPermission(string $status, bool $canExport, bool $canImport, bool $expectException): void
    {
        /* @var UpdateRequest&MockObject $request */
        $request = $this->createMock(UpdateRequest::class);
        $request->expects($this->any())
                ->method('getStatus')
                ->willReturn($status);

        /* @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->expects($this->any())
              ->method('getCanExport')
              ->willReturn($canExport);
        $agent->expects($this->any())
              ->method('getCanImport')
              ->willReturn($canImport);

        if ($expectException) {
            $this->expectException(ActionNotAllowedException::class);
        } else {
            $this->addToAssertionCount(1);
        }

        $handler = new UpdateHandler($this->jobRepository, $this->mapperManager);
        $this->invokeMethod($handler, 'checkPermission', $request, $agent);
    }

    /**
     * Tests the modifyJobEntity method.
     * @throws ReflectionException
     * @covers ::modifyJobEntity
     */
    public function testModifyJobEntity(): void
    {
        $status = JobStatus::DONE;
        $errorMessage = 'abc';
        $agentName = 'def';

        /* @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->expects($this->any())
              ->method('getName')
              ->willReturn($agentName);

        /* @var UpdateRequest&MockObject $request */
        $request = $this->createMock(UpdateRequest::class);
        $request->expects($this->any())
                ->method('getStatus')
                ->willReturn($status);
        $request->expects($this->any())
                ->method('getErrorMessage')
                ->willReturn($errorMessage);

        /* @var Job&MockObject $job */
        $job = $this->createMock(Job::class);
        $job->expects($this->once())
            ->method('setStatus')
            ->with($this->identicalTo($status))
            ->willReturnSelf();
        $job->expects($this->once())
            ->method('setErrorMessage')
            ->with($this->identicalTo($errorMessage))
            ->willReturnSelf();
        $job->expects($this->never())
            ->method('setExporter');
        $job->expects($this->never())
            ->method('setExportTime');
        $job->expects($this->never())
            ->method('setImporter');
        $job->expects($this->never())
            ->method('setImportTime');

        $handler = new UpdateHandler($this->jobRepository, $this->mapperManager);
        $this->invokeMethod($handler, 'modifyJobEntity', $job, $request, $agent);
    }

    /**
     * Tests the modifyJobEntity method.
     * @throws ReflectionException
     * @covers ::modifyJobEntity
     */
    public function testModifyJobEntityDownloading(): void
    {
        $status = JobStatus::DOWNLOADING;
        $errorMessage = 'abc';
        $agentName = 'def';

        /* @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->expects($this->any())
              ->method('getName')
              ->willReturn($agentName);

        /* @var UpdateRequest&MockObject $request */
        $request = $this->createMock(UpdateRequest::class);
        $request->expects($this->any())
                ->method('getStatus')
                ->willReturn($status);
        $request->expects($this->any())
                ->method('getErrorMessage')
                ->willReturn($errorMessage);

        /* @var Job&MockObject $job */
        $job = $this->createMock(Job::class);
        $job->expects($this->once())
            ->method('setStatus')
            ->with($this->identicalTo($status))
            ->willReturnSelf();
        $job->expects($this->once())
            ->method('setErrorMessage')
            ->with($this->identicalTo($errorMessage))
            ->willReturnSelf();
        $job->expects($this->once())
            ->method('setExporter')
            ->with($this->identicalTo($agentName))
            ->willReturnSelf();
        $job->expects($this->once())
            ->method('setExportTime')
            ->with($this->isInstanceOf(DateTime::class))
            ->willReturnSelf();
        $job->expects($this->never())
            ->method('setImporter');
        $job->expects($this->never())
            ->method('setImportTime');

        $handler = new UpdateHandler($this->jobRepository, $this->mapperManager);
        $this->invokeMethod($handler, 'modifyJobEntity', $job, $request, $agent);
    }

    /**
     * Tests the modifyJobEntity method.
     * @throws ReflectionException
     * @covers ::modifyJobEntity
     */
    public function testModifyJobEntityImporting(): void
    {
        $status = JobStatus::IMPORTING;
        $errorMessage = 'abc';
        $agentName = 'def';

        /* @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->expects($this->any())
              ->method('getName')
              ->willReturn($agentName);

        /* @var UpdateRequest&MockObject $request */
        $request = $this->createMock(UpdateRequest::class);
        $request->expects($this->any())
                ->method('getStatus')
                ->willReturn($status);
        $request->expects($this->any())
                ->method('getErrorMessage')
                ->willReturn($errorMessage);

        /* @var Job&MockObject $job */
        $job = $this->createMock(Job::class);
        $job->expects($this->once())
            ->method('setStatus')
            ->with($this->identicalTo($status))
            ->willReturnSelf();
        $job->expects($this->once())
            ->method('setErrorMessage')
            ->with($this->identicalTo($errorMessage))
            ->willReturnSelf();
        $job->expects($this->never())
            ->method('setExporter');
        $job->expects($this->never())
            ->method('setExportTime');
        $job->expects($this->once())
            ->method('setImporter')
            ->with($this->identicalTo($agentName))
            ->willReturnSelf();
        $job->expects($this->once())
            ->method('setImportTime')
            ->with($this->isInstanceOf(DateTime::class))
            ->willReturnSelf();

        $handler = new UpdateHandler($this->jobRepository, $this->mapperManager);
        $this->invokeMethod($handler, 'modifyJobEntity', $job, $request, $agent);
    }
}

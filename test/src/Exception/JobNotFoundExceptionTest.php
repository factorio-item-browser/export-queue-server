<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Exception;

use FactorioItemBrowser\ExportQueue\Server\Exception\JobNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Throwable;

/**
 * The PHPUnit test of the JobNotFoundException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Exception\JobNotFoundException
 */
class JobNotFoundExceptionTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        /* @var UuidInterface&MockObject $jobId */
        $jobId = $this->createMock(UuidInterface::class);
        $jobId->expects($this->once())
              ->method('toString')
              ->willReturn('abc');

        /* @var Throwable&MockObject $previous */
        $previous = $this->createMock(Throwable::class);

        $exception = new JobNotFoundException($jobId, $previous);

        $this->assertSame('Job abc not found.', $exception->getMessage());
        $this->assertSame(404, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}

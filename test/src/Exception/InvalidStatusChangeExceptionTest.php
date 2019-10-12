<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Exception;

use FactorioItemBrowser\ExportQueue\Server\Exception\InvalidStatusChangeException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the InvalidStatusChangeException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Exception\InvalidStatusChangeException
 */
class InvalidStatusChangeExceptionTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $currentStatus = 'abc';
        $newStatus = 'def';
        /* @var Throwable&MockObject $previous */
        $previous = $this->createMock(Throwable::class);

        $exception = new InvalidStatusChangeException($currentStatus, $newStatus, $previous);

        $this->assertSame('Status change from abc to def is not allowed.', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}

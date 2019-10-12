<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Exception;

use FactorioItemBrowser\ExportQueue\Server\Exception\InvalidAgentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the InvalidAgentException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Exception\InvalidAgentException
 */
class InvalidAgentExceptionTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        /* @var Throwable&MockObject $previous */
        $previous = $this->createMock(Throwable::class);

        $exception = new InvalidAgentException($previous);

        $this->assertSame('Invalid access key specified. Access denied.', $exception->getMessage());
        $this->assertSame(403, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}

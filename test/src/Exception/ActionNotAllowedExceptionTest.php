<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Exception;

use FactorioItemBrowser\ExportQueue\Server\Exception\ActionNotAllowedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The PHPUnit test of the ActionNotAllowedException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Exception\ActionNotAllowedException
 */
class ActionNotAllowedExceptionTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $action = 'abc';
        /* @var Throwable&MockObject $previous */
        $previous = $this->createMock(Throwable::class);

        $exception = new ActionNotAllowedException($action, $previous);

        $this->assertSame('abc is not allowed by the current agent.', $exception->getMessage());
        $this->assertSame(403, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}

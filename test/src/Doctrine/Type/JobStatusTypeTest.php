<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Doctrine\Type;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use FactorioItemBrowser\ExportQueue\Server\Doctrine\Type\JobStatusType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the JobStatusType class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Doctrine\Type\JobStatusType
 */
class JobStatusTypeTest extends TestCase
{
    /**
     * The instance to test against
     * @var JobStatusType
     */
    protected static $type;

    /**
     * Sets up the test case.
     * @throws DBALException
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // We have to use the factory methods because Doctrine.
        Type::addType(JobStatusType::NAME, JobStatusType::class);
        self::$type = Type::getType(JobStatusType::NAME);
    }

    /**
     * Tests the getSQLDeclaration method.
     * @covers ::getSQLDeclaration
     */
    public function testGetSQLDeclaration(): void
    {
        $fieldDeclaration = ['abc'];
        $expectedResult = 'ENUM("queued","downloading","processing","uploading","uploaded","importing","done","error")';

        /* @var AbstractPlatform&MockObject $platform */
        $platform = $this->createMock(AbstractPlatform::class);
        $platform->expects($this->any())
                 ->method('quoteStringLiteral')
                 ->with($this->isType('string'))
                 ->willReturnCallback(function (string $value): string {
                     return sprintf('"%s"', $value);
                 });

        $result = self::$type->getSQLDeclaration($fieldDeclaration, $platform);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the getName method.
     * @covers ::getName
     */
    public function testGetName(): void
    {
        $expectedResult = 'job_status';

        $result = self::$type->getName();

        $this->assertSame($expectedResult, $result);
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Entity;

use DateTime;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Job class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Entity\Job
 */
class JobTest extends TestCase
{
    /**
     * Tests the constructing.
     * @coversNothing
     */
    public function testConstruct(): void
    {
        $entity = new Job();

        $this->assertNull($entity->getId());
        $this->assertNull($entity->getCombinationId());
        $this->assertSame([], $entity->getModNames());
        $this->assertSame('', $entity->getPriority());
        $this->assertSame('', $entity->getStatus());
        $this->assertSame('', $entity->getErrorMessage());
        $this->assertSame('', $entity->getCreator());
        $this->assertNull($entity->getCreationTime());
        $this->assertSame('', $entity->getExporter());
        $this->assertNull($entity->getExportTime());
        $this->assertSame('', $entity->getImporter());
        $this->assertNull($entity->getImportTime());
    }

    /**
     * Tests the setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);
        $entity = new Job();

        $this->assertSame($entity, $entity->setId($id));
        $this->assertSame($id, $entity->getId());
    }

    /**
     * Tests the setting and getting the combination id.
     * @covers ::getCombinationId
     * @covers ::setCombinationId
     */
    public function testSetAndGetCombinationId(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        $entity = new Job();

        $this->assertSame($entity, $entity->setCombinationId($combinationId));
        $this->assertSame($combinationId, $entity->getCombinationId());
    }

    /**
     * Tests the setting and getting the mod names.
     * @covers ::getModNames
     * @covers ::setModNames
     */
    public function testSetAndGetModNames(): void
    {
        $modNames = ['abc', 'def'];
        $entity = new Job();

        $this->assertSame($entity, $entity->setModNames($modNames));
        $this->assertSame($modNames, $entity->getModNames());
    }

    /**
     * Tests the setting and getting the priority.
     * @covers ::getPriority
     * @covers ::setPriority
     */
    public function testSetAndGetPriority(): void
    {
        $priority = 'abc';
        $entity = new Job();

        $this->assertSame($entity, $entity->setPriority($priority));
        $this->assertSame($priority, $entity->getPriority());
    }

    /**
     * Tests the setting and getting the status.
     * @covers ::getStatus
     * @covers ::setStatus
     */
    public function testSetAndGetStatus(): void
    {
        $status = 'abc';
        $entity = new Job();

        $this->assertSame($entity, $entity->setStatus($status));
        $this->assertSame($status, $entity->getStatus());
    }

    /**
     * Tests the setting and getting the error message.
     * @covers ::getErrorMessage
     * @covers ::setErrorMessage
     */
    public function testSetAndGetErrorMessage(): void
    {
        $errorMessage = 'abc';
        $entity = new Job();

        $this->assertSame($entity, $entity->setErrorMessage($errorMessage));
        $this->assertSame($errorMessage, $entity->getErrorMessage());
    }

    /**
     * Tests the setting and getting the creator.
     * @covers ::getCreator
     * @covers ::setCreator
     */
    public function testSetAndGetCreator(): void
    {
        $creator = 'abc';
        $entity = new Job();

        $this->assertSame($entity, $entity->setCreator($creator));
        $this->assertSame($creator, $entity->getCreator());
    }

    /**
     * Tests the setting and getting the creation time.
     * @covers ::getCreationTime
     * @covers ::setCreationTime
     */
    public function testSetAndGetCreationTime(): void
    {
        $creationTime = new DateTime('2038-01-19 03:14:07');
        $entity = new Job();

        $this->assertSame($entity, $entity->setCreationTime($creationTime));
        $this->assertSame($creationTime, $entity->getCreationTime());
    }

    /**
     * Tests the setting and getting the exporter.
     * @covers ::getExporter
     * @covers ::setExporter
     */
    public function testSetAndGetExporter(): void
    {
        $exporter = 'abc';
        $entity = new Job();

        $this->assertSame($entity, $entity->setExporter($exporter));
        $this->assertSame($exporter, $entity->getExporter());
    }

    /**
     * Tests the setting and getting the export time.
     * @covers ::getExportTime
     * @covers ::setExportTime
     */
    public function testSetAndGetExportTime(): void
    {
        $exportTime = new DateTime('2038-01-19 03:14:07');
        $entity = new Job();

        $this->assertSame($entity, $entity->setExportTime($exportTime));
        $this->assertSame($exportTime, $entity->getExportTime());
    }

    /**
     * Tests the setting and getting the importer.
     * @covers ::getImporter
     * @covers ::setImporter
     */
    public function testSetAndGetImporter(): void
    {
        $importer = 'abc';
        $entity = new Job();

        $this->assertSame($entity, $entity->setImporter($importer));
        $this->assertSame($importer, $entity->getImporter());
    }

    /**
     * Tests the setting and getting the import time.
     * @covers ::getImportTime
     * @covers ::setImportTime
     */
    public function testSetAndGetImportTime(): void
    {
        $importTime = new DateTime('2038-01-19 03:14:07');
        $entity = new Job();

        $this->assertSame($entity, $entity->setImportTime($importTime));
        $this->assertSame($importTime, $entity->getImportTime());
    }
}

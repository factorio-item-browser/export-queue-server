<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Entity;

use FactorioItemBrowser\ExportQueue\Server\Entity\Agent;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the Agent class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Entity\Agent
 */
class AgentTest extends TestCase
{
    /**
     * Tests the constructing.
     * @coversNothing
     */
    public function testConstruct(): void
    {
        $entity = new Agent();

        $this->assertSame('', $entity->getName());
        $this->assertSame('', $entity->getAccessKey());
        $this->assertFalse($entity->getCanCreate());
        $this->assertFalse($entity->getCanExport());
        $this->assertFalse($entity->getCanImport());
    }

    /**
     * Tests the setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $entity = new Agent();

        $this->assertSame($entity, $entity->setName($name));
        $this->assertSame($name, $entity->getName());
    }

    /**
     * Tests the setting and getting the access key.
     * @covers ::getAccessKey
     * @covers ::setAccessKey
     */
    public function testSetAndGetAccessKey(): void
    {
        $accessKey = 'abc';
        $entity = new Agent();

        $this->assertSame($entity, $entity->setAccessKey($accessKey));
        $this->assertSame($accessKey, $entity->getAccessKey());
    }

    /**
     * Tests the setting and getting the can create.
     * @covers ::getCanCreate
     * @covers ::setCanCreate
     */
    public function testSetAndGetCanCreate(): void
    {
        $canCreate = true;
        $entity = new Agent();

        $this->assertSame($entity, $entity->setCanCreate($canCreate));
        $this->assertSame($canCreate, $entity->getCanCreate());
    }

    /**
     * Tests the setting and getting the can export.
     * @covers ::getCanExport
     * @covers ::setCanExport
     */
    public function testSetAndGetCanExport(): void
    {
        $canExport = true;
        $entity = new Agent();

        $this->assertSame($entity, $entity->setCanExport($canExport));
        $this->assertSame($canExport, $entity->getCanExport());
    }

    /**
     * Tests the setting and getting the can import.
     * @covers ::getCanImport
     * @covers ::setCanImport
     */
    public function testSetAndGetCanImport(): void
    {
        $canImport = true;
        $entity = new Agent();

        $this->assertSame($entity, $entity->setCanImport($canImport));
        $this->assertSame($canImport, $entity->getCanImport());
    }
}

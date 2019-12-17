<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Mapper;

use DateTime;
use FactorioItemBrowser\ExportQueue\Client\Entity\Job as ClientJob;
use FactorioItemBrowser\ExportQueue\Client\Response\Job\DetailsResponse;
use FactorioItemBrowser\ExportQueue\Client\Response\Job\ListResponse;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job as JobEntity;
use FactorioItemBrowser\ExportQueue\Server\Mapper\JobMapper;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the JobMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Mapper\JobMapper
 */
class JobMapperTest extends TestCase
{
    /**
     * Provides the data for the supports test.
     * @return array<mixed>
     */
    public function provideSupports(): array
    {
        return [
            [new JobEntity(), new ClientJob(), true],
            [new JobEntity(), new DetailsResponse(), true],
            [new JobEntity(), new ListResponse(), false],
            [$this, new ClientJob(), false],
        ];
    }

    /**
     * Tests the supports method.
     * @param object $source
     * @param object $destination
     * @param bool $expectedResult
     * @covers ::supports
     * @dataProvider provideSupports
     */
    public function testSupports(object $source, object $destination, bool $expectedResult): void
    {
        $mapper = new JobMapper();
        $result = $mapper->supports($source, $destination);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the map method.
     * @covers ::map
     */
    public function testMap(): void
    {
        $entity = new JobEntity();
        $entity->setId(Uuid::fromString('01234567-0123-0123-0123-0123456789ab'))
               ->setCombinationId(Uuid::fromString('fedcba98-fedc-fedc-fedc-fedcba987654'))
               ->setModNames(['abc', 'def'])
               ->setStatus('ghi')
               ->setErrorMessage('jkl')
               ->setCreator('mno')
               ->setCreationTime(new DateTime('2038-01-17 03:14:07'))
               ->setExporter('pqr')
               ->setExportTime(new DateTime('2038-01-18 03:14:07'))
               ->setImporter('stu')
               ->setImportTime(new DateTime('2038-01-19 03:14:07'));

        $expectedResult = new ClientJob();
        $expectedResult->setId('01234567-0123-0123-0123-0123456789ab')
                       ->setCombinationId('fedcba98-fedc-fedc-fedc-fedcba987654')
                       ->setModNames(['abc', 'def'])
                       ->setStatus('ghi')
                       ->setErrorMessage('jkl')
                       ->setCreator('mno')
                       ->setCreationTime(new DateTime('2038-01-17 03:14:07'))
                       ->setExporter('pqr')
                       ->setExportTime(new DateTime('2038-01-18 03:14:07'))
                       ->setImporter('stu')
                       ->setImportTime(new DateTime('2038-01-19 03:14:07'));
        
        $destination = new ClientJob();
        
        $mapper = new JobMapper();
        $mapper->map($entity, $destination);
        
        $this->assertEquals($expectedResult, $destination);
    }
}

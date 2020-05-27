<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Mapper;

use BluePsyduck\MapperManager\Mapper\DynamicMapperInterface;
use FactorioItemBrowser\ExportQueue\Client\Entity\Job as ClientJob;
use FactorioItemBrowser\ExportQueue\Server\Entity\Job as JobEntity;

/**
 * The mapper of the job entity.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class JobMapper implements DynamicMapperInterface
{
    /**
     * Returns whether the mapper supports the combination of source and destination object.
     * @param object $source
     * @param object $destination
     * @return bool
     */
    public function supports($source, $destination): bool
    {
        return $source instanceof JobEntity && $destination instanceof ClientJob;
    }

    /**
     * Maps the source object to the destination one.
     * @param JobEntity $source
     * @param ClientJob $destination
     */
    public function map($source, $destination): void
    {
        if ($source->getId() !== null) {
            $destination->setId($source->getId()->toString());
        }
        if ($source->getCombinationId() !== null) {
            $destination->setCombinationId($source->getCombinationId()->toString());
        }

        $destination->setModNames($source->getModNames())
                    ->setPriority($source->getPriority())
                    ->setStatus($source->getStatus())
                    ->setErrorMessage($source->getErrorMessage())
                    ->setCreator($source->getCreator())
                    ->setCreationTime($source->getCreationTime())
                    ->setExporter($source->getExporter())
                    ->setExportTime($source->getExportTime())
                    ->setImporter($source->getImporter())
                    ->setImportTime($source->getImportTime());
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use FactorioItemBrowser\ExportQueue\Client\Constant\JobStatus as JobStatusConstant;

/**
 * The enum type for the job status.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class JobStatus extends Type
{
    /**
     * The name of the Doctrine type.
     */
    public const NAME = 'job_status';

    /**
     * The values of the enum.
     */
    protected const VALUES = [
        JobStatusConstant::QUEUED,
        JobStatusConstant::DOWNLOADING,
        JobStatusConstant::PROCESSING,
        JobStatusConstant::UPLOADING,
        JobStatusConstant::UPLOADED,
        JobStatusConstant::IMPORTING,
        JobStatusConstant::DONE,
        JobStatusConstant::ERROR,
    ];

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param mixed[] $fieldDeclaration The field declaration.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = implode(',', array_map(function(string $value) use ($platform): string {
            return $platform->quoteStringLiteral($value);
        }, self::VALUES));

        return sprintf('ENUM(%s)', $values);
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}

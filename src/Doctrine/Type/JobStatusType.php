<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Doctrine\Type;

use FactorioItemBrowser\ExportQueue\Client\Constant\JobStatus;

/**
 * The enum type for the job status.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class JobStatusType extends AbstractEnumType
{
    /**
     * The name of the Doctrine type.
     */
    public const NAME = 'job_status';

    /**
     * The values of the enum.
     */
    public const VALUES = [
        JobStatus::QUEUED,
        JobStatus::DOWNLOADING,
        JobStatus::PROCESSING,
        JobStatus::UPLOADING,
        JobStatus::UPLOADED,
        JobStatus::IMPORTING,
        JobStatus::DONE,
        JobStatus::ERROR,
    ];
}

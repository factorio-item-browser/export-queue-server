<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Doctrine\Type;

use FactorioItemBrowser\ExportQueue\Client\Constant\JobPriority;

/**
 * The enum type for the job priority.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class JobPriorityType extends AbstractEnumType
{
    /**
     * The name of the Doctrine type.
     */
    public const NAME = 'job_priority';

    /**
     * The values of the enum.
     */
    public const VALUES = [
        JobPriority::ADMIN,
        JobPriority::USER,
        JobPriority::SCRIPT,
    ];
}

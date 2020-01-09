<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Constant;

/**
 * The interface holding all the route names.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface RouteName
{
    public const JOB_CREATE = 'job.create';
    public const JOB_DETAILS = 'job.details';
    public const JOB_LIST = 'job.list';
    public const JOB_UPDATE = 'job.update';
}

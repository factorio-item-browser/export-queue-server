<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Constant;

/**
 *
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ConfigKey
{
    /**
     * The key holding the name of the project.
     */
    public const PROJECT = 'factorio-item-browser';

    /**
     * The key holding the name of the export queue server itself.
     */
    public const EXPORT_QUEUE_SERVER = 'export-queue-server';

    /**
     * The key holding the map of request classes to their routes.
     */
    public const REQUEST_CLASSES_BY_ROUTES = 'request-classes-by-routes';

}

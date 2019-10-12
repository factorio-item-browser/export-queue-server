<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Constant;

/**
 * The interface holding the keys used in the config.
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

    /**
     * The key holding the agent config.
     */
    public const AGENTS = 'agents';

    /**
     * The key holding the name of the agent.
     */
    public const AGENT_NAME = 'name';

    /**
     * The key holding the access key of the agent.
     */
    public const AGENT_ACCESS_KEY = 'access-key';
}

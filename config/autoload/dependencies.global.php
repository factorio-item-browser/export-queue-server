<?php

declare(strict_types=1);

/**
 * The configuration of the project dependencies.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\ExportQueue\Server;

use BluePsyduck\ZendAutoWireFactory\AutoWireFactory;
use FactorioItemBrowser\ExportQueue\Server\Constant\ConfigKey;
use function BluePsyduck\ZendAutoWireFactory\readConfig;

return [
    'dependencies' => [
        'factories' => [
            Handler\Job\AddHandler::class => AutoWireFactory::class,
            Handler\Job\GetHandler::class => AutoWireFactory::class,
            Handler\Job\ListHandler::class => AutoWireFactory::class,
            Handler\Job\UpdateHandler::class => AutoWireFactory::class,
            Handler\Node\PingHandler::class => AutoWireFactory::class,

            Middleware\RequestDeserializerMiddleware::class => AutoWireFactory::class,
            Middleware\ResponseSerializerMiddleware::class => AutoWireFactory::class,

            // Auto-wire helpers
            'array $requestClassesByRoutes' => readConfig(ConfigKey::PROJECT, ConfigKey::EXPORT_QUEUE_SERVER, ConfigKey::REQUEST_CLASSES_BY_ROUTES),
        ],
    ],
];

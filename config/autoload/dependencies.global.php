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

return [
    'dependencies' => [
        'factories' => [
            Handler\Job\AddHandler::class => AutoWireFactory::class,
            Handler\Job\GetHandler::class => AutoWireFactory::class,
            Handler\Job\ListHandler::class => AutoWireFactory::class,
            Handler\Job\UpdateHandler::class => AutoWireFactory::class,
            Handler\Node\PingHandler::class => AutoWireFactory::class,

            Middleware\ResponseSerializerMiddleware::class => AutoWireFactory::class,
        ],
    ],
];

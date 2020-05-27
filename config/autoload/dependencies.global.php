<?php

declare(strict_types=1);

/**
 * The configuration of the project dependencies.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\ExportQueue\Server;

use BluePsyduck\LaminasAutoWireFactory\AutoWireFactory;
use ContainerInteropDoctrine\EntityManagerFactory;
use Doctrine\ORM\EntityManagerInterface;
use FactorioItemBrowser\ExportQueue\Server\Constant\ConfigKey;
use Mezzio\Middleware\ErrorResponseGenerator;
use Roave\PsrContainerDoctrine\MigrationsConfigurationFactory;

use function BluePsyduck\LaminasAutoWireFactory\readConfig;

return [
    'dependencies' => [
        'aliases' => [
            ErrorResponseGenerator::class => Response\ErrorResponseGenerator::class,
        ],
        'factories' => [
            Handler\Job\CreateHandler::class => AutoWireFactory::class,
            Handler\Job\DetailsHandler::class => AutoWireFactory::class,
            Handler\Job\ListHandler::class => AutoWireFactory::class,
            Handler\Job\UpdateHandler::class => AutoWireFactory::class,

            Mapper\JobMapper::class => AutoWireFactory::class,

            Middleware\AgentMiddleware::class => AutoWireFactory::class,
            Middleware\MetaMiddleware::class => AutoWireFactory::class,
            Middleware\RequestDeserializerMiddleware::class => AutoWireFactory::class,
            Middleware\ResponseSerializerMiddleware::class => AutoWireFactory::class,

            Repository\AgentRepository::class => Repository\AgentRepositoryFactory::class,
            Repository\JobRepository::class => AutoWireFactory::class,

            Response\ErrorResponseGenerator::class => AutoWireFactory::class,

            // 3rd-party dependencies
            EntityManagerInterface::class => EntityManagerFactory::class,
            'doctrine.migrations.orm_default' => MigrationsConfigurationFactory::class,

            // Auto-wire helpers
            'array $requestClassesByRoutes' => readConfig(ConfigKey::PROJECT, ConfigKey::EXPORT_QUEUE_SERVER, ConfigKey::REQUEST_CLASSES_BY_ROUTES),
            'bool $isDebug' => readConfig('debug'),
            'string $version' => readConfig('version'),
        ],
    ],
];

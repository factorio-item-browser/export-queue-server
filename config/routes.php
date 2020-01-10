<?php

declare(strict_types=1);

/**
 * The file providing the routes.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\ExportQueue\Server;

use FactorioItemBrowser\ExportQueue\Server\Constant\RouteName;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $regexUuid = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

    $app->post('/job', Handler\Job\CreateHandler::class, RouteName::JOB_CREATE);
    $app->get(sprintf('/job/{job-id:%s}', $regexUuid), Handler\Job\DetailsHandler::class, RouteName::JOB_DETAILS);
    $app->patch(sprintf('/job/{job-id:%s}', $regexUuid), Handler\Job\UpdateHandler::class, RouteName::JOB_UPDATE);
    $app->get('/job/list', Handler\Job\ListHandler::class, RouteName::JOB_LIST);
};

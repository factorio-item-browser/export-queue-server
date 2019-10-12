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
use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->post('/job', Handler\Job\AddHandler::class, RouteName::JOB_ADD);
    $app->get('/job/{job-id:[0-9a-f-]+}', Handler\Job\GetHandler::class, RouteName::JOB_GET);
    $app->patch('/job/{job-id:[0-9a-f-]+}', Handler\Job\UpdateHandler::class, RouteName::JOB_UPDATE);
    $app->get('/job/list', Handler\Job\ListHandler::class, RouteName::JOB_LIST);
};

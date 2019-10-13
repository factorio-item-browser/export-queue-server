<?php

declare(strict_types=1);

/**
 * The configuration of the Export Queue.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\ExportQueue\Server;

use FactorioItemBrowser\ExportQueue\Client\Request;
use FactorioItemBrowser\ExportQueue\Server\Constant\ConfigKey;
use FactorioItemBrowser\ExportQueue\Server\Constant\RouteName;

return [
    ConfigKey::PROJECT => [
        ConfigKey::EXPORT_QUEUE_SERVER => [
            ConfigKey::REQUEST_CLASSES_BY_ROUTES => [
                RouteName::JOB_CREATE => Request\Job\CreateRequest::class,
                RouteName::JOB_DETAILS => Request\Job\DetailsRequest::class,
                RouteName::JOB_LIST => Request\Job\ListRequest::class,
                RouteName::JOB_UPDATE => Request\Job\UpdateRequest::class,
            ],
        ],
    ],
];

<?php

declare(strict_types=1);

/**
 * The configuration of doctrine.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\ExportQueue\Server;

use Doctrine\DBAL\Driver\PDOMySql\Driver as PDOMySqlDriver;
use PDO;

return [
    'doctrine' => [
//        'configuration' => [
//            'orm_default' => [
//                'metadata_cache' => 'filesystem',
//                'query_cache' => 'filesystem',
//            ]
//        ],
        'connection' => [
            'orm_default' => [
                'driverClass' => PDOMySqlDriver::class,
                'params' => [
                    'host'     => 'fib-eq-mysql',
                    'port'     => '3306',
                    'user'     => 'docker',
                    'password' => 'docker',
                    'dbname'   => 'docker',
                    'driverOptions' => [
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                    ]
                ]
            ]
        ],
//        'driver' => [
//            'orm_default' => [
//                'cache' => 'filesystem',
//            ]
//        ],
    ],
];

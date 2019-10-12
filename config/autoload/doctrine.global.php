<?php

declare(strict_types=1);

/**
 * The configuration of the Doctrine integration.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\ExportQueue\Server;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Ramsey\Uuid\Doctrine\UuidBinaryType;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'doctrine_mapping_types' => [
                    UuidBinaryType::NAME => UuidBinaryType::BINARY,
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => MappingDriverChain::class,
                'drivers' => [
                    'FactorioItemBrowser\ExportQueue\Server\Entity' => 'fib-export-queue-database',
                ],
            ],

            'fib-export-queue-database' => [
                'class' => SimplifiedXmlDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../../config/doctrine' => 'FactorioItemBrowser\ExportQueue\Server\Entity',
                ],
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => __DIR__ . '/../../data/migrations',
                'name'      => 'FactorioItemBrowser ExportQueue Database Migrations',
                'namespace' => 'FactorioItemBrowser\ExportQueue\Server\Migrations',
                'table'     => '_Migrations',
            ],
        ],
        'types' => [
            Doctrine\Type\JobStatus::NAME => Doctrine\Type\JobStatus::class,
            UuidBinaryType::NAME => UuidBinaryType::class,
        ],
    ],
];

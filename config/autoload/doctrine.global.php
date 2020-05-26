<?php

declare(strict_types=1);

/**
 * The configuration of the Doctrine integration.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\ExportQueue\Server;

use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Ramsey\Uuid\Doctrine\UuidBinaryType;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'doctrine_mapping_types' => [
                    UuidBinaryType::NAME => UuidBinaryType::BINARY,
                    'enum' => 'string',
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => SimplifiedXmlDriver::class,
                'cache' => 'array',
                'paths' => [
                    'config/doctrine' => 'FactorioItemBrowser\ExportQueue\Server\Entity',
                ],
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/migrations',
                'name'      => 'FactorioItemBrowser ExportQueue Database Migrations',
                'namespace' => 'FactorioItemBrowser\ExportQueue\Server\Migrations',
                'table'     => '_Migrations',
            ],
        ],
        'types' => [
            Doctrine\Type\JobPriorityType::NAME => Doctrine\Type\JobPriorityType::class,
            Doctrine\Type\JobStatusType::NAME => Doctrine\Type\JobStatusType::class,
            UuidBinaryType::NAME => UuidBinaryType::class,
        ],
    ],
];

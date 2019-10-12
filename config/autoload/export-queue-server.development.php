<?php

declare(strict_types=1);

/**
 * The configuration of the Export Queue.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\ExportQueue\Server;

use FactorioItemBrowser\ExportQueue\Server\Constant\ConfigKey;

return [
    ConfigKey::PROJECT => [
        ConfigKey::EXPORT_QUEUE_SERVER => [
            ConfigKey::AGENTS => [
                [
                    ConfigKey::AGENT_NAME => 'debug',
                    ConfigKey::AGENT_ACCESS_KEY => 'factorio-item-browser',
                    ConfigKey::AGENT_CAN_CREATE => true,
                    ConfigKey::AGENT_CAN_EXPORT => true,
                    ConfigKey::AGENT_CAN_IMPORT => true,
                ],
            ],
        ],
    ],
];

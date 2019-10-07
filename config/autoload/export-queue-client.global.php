<?php

declare(strict_types=1);

/**
 * The configuration of the Export Queue.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\ExportQueue\Server;

use FactorioItemBrowser\ExportQueue\Client\Constant\ConfigKey;

return [
    ConfigKey::PROJECT => [
        ConfigKey::EXPORT_QUEUE_CLIENT => [
            ConfigKey::CACHE_DIR => __DIR__ . '/../../data/cache/export-queue-client',
        ],
    ],
];

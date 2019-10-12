<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Exception;

use Throwable;

/**
 * The exception thrown when an invalid status change is attempted.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class InvalidStatusChangeException extends ExportQueueServerException
{
    /**
     * The message template of the exception.
     */
    protected const MESSAGE = 'Status change from %s to %s is not allowed.';

    /**
     * Initializes the exception.
     * @param string $currentStatus
     * @param string $newStatus
     * @param Throwable|null $previous
     */
    public function __construct(string $currentStatus, string $newStatus, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $currentStatus, $newStatus), 400, $previous);
    }
}

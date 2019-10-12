<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Exception;

use Throwable;

/**
 * The exception thrown when an invalid agent was encountered.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class InvalidAgentException extends ExportQueueServerException
{
    /**
     * The message template of the exception.
     */
    protected const MESSAGE = 'Invalid access key specified. Access denied.';

    /**
     * Initializes the exception.
     * @param Throwable|null $previous
     */
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, 403, $previous);
    }
}

<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Exception;

use Throwable;

/**
 * The exception thrown when a requested job was not found.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class JobNotFoundException extends ExportQueueServerException
{
    /**
     * The message template of the exception.
     */
    protected const MESSAGE = 'Job #%d not found.';

    /**
     * Initializes the exception.
     * @param int $jobId
     * @param Throwable|null $previous
     */
    public function __construct(int $jobId, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $jobId), 404, $previous);
    }
}

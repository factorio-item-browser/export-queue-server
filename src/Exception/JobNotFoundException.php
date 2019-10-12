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
    protected const MESSAGE = 'Job %s not found.';

    /**
     * Initializes the exception.
     * @param string $jobId
     * @param Throwable|null $previous
     */
    public function __construct(string $jobId, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $jobId), 404, $previous);
    }
}

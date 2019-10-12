<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Exception;

use Ramsey\Uuid\UuidInterface;
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
     * @param UuidInterface $jobId
     * @param Throwable|null $previous
     */
    public function __construct(UuidInterface $jobId, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $jobId->toString()), 404, $previous);
    }
}

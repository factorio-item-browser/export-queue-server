<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Exception;

use Throwable;

/**
 * The exception thrown when an action is not allowed by an agent.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ActionNotAllowedException extends ExportQueueServerException
{
    /**
     * The message template of the exception.
     */
    protected const MESSAGE = '%s is not allowed by the current agent.';

    /**
     * Initializes the exception.
     * @param string $action
     * @param Throwable|null $previous
     */
    public function __construct(string $action, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $action), 403, $previous);
    }
}

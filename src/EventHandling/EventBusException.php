<?php

declare(strict_types=1);

namespace Shared\EventHandling;

use Shared\Exception\InfrastructureException;

/**
 * Raised when publishing on the event bus fails.
 */
final class EventBusException extends InfrastructureException
{
    public static function fromThrowable(\Throwable $e): self
    {
        return new self($e->getMessage(), previous: $e);
    }
}

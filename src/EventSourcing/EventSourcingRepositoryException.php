<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Exception\InfrastructureException;

/**
 * Raised when loading or saving an aggregate through the event store
 * fails.
 */
final class EventSourcingRepositoryException extends InfrastructureException
{
    public static function throwable(\Throwable $e): self
    {
        return new self($e->getMessage(), previous: $e);
    }
}

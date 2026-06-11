<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Exception\InfrastructureException;

final class EventStoreException extends InfrastructureException
{
    public static function throwable(\Throwable $e): self
    {
        return new self($e->getMessage(), previous: $e);
    }
}

<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Exception\InfrastructureException;

final class EventSourcingRepositoryException extends InfrastructureException
{
    public static function throwable(\Throwable $e): self
    {
        return new self($e->getMessage(), previous: $e);
    }
}

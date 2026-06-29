<?php

declare(strict_types=1);

namespace Shared\Snapshotting;

use Shared\Domain\Uuid;
use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;
use Shared\Exception\InfrastructureException;

/**
 * Raised when a stored snapshot cannot be used to rebuild its aggregate, e.g. it
 * does not carry an event-sourced aggregate. The snapshot data is corrupt or was
 * written by an incompatible version.
 */
final class CorruptSnapshotException extends InfrastructureException
{
    public static function notAnAggregateRoot(Uuid $id): self
    {
        return new self(\sprintf('Snapshot for "%s" must carry an aggregate extending %s.', $id->uuid, AbstractEventSourcedAggregateRoot::class));
    }

    public static function fromThrowable(\Throwable $e): self
    {
        return new self($e->getMessage(), previous: $e);
    }
}

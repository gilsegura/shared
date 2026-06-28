<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventStream;
use Shared\Domain\Uuid;

/**
 * An aggregate root: identified by a Uuid, records uncommitted events and
 * rebuilds from a stream. The id is part of the contract because the aggregate
 * stamps it onto every DomainMessage it records.
 */
interface AggregateRootInterface
{
    public function id(): Uuid;

    public function uncommittedEvents(): DomainEventStream;
}

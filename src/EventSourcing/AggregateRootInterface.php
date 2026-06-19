<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventStream;
use Shared\Domain\IdentifiableInterface;

/**
 * An aggregate root: identifiable, records uncommitted events and rebuilds
 * from a stream.
 */
interface AggregateRootInterface extends IdentifiableInterface
{
    public function uncommittedEvents(): DomainEventStream;
}

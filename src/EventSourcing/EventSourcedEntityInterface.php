<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventInterface;

interface EventSourcedEntityInterface
{
    public function handleRecursively(DomainEventInterface $event): void;

    /**
     * @throws AggregateRootAlreadyExistsException
     */
    public function setAggregateRoot(AbstractEventSourcedAggregateRoot $aggregateRoot): void;
}

<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\DomainEventStream;
use Shared\Domain\Uuid;

/**
 * Loads and appends domain event streams by aggregate id.
 */
interface EventStoreInterface
{
    /**
     * @throws EventStoreException
     * @throws StreamNotFoundException
     */
    public function load(Uuid $id, ?int $playhead = null): DomainEventStream;

    /**
     * @throws EventStoreException
     * @throws StreamAlreadyExistsException
     */
    public function append(DomainEventStream $stream): void;
}

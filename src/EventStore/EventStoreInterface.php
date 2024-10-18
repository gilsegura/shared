<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\DomainEventStream;
use Shared\Domain\Uuid;

interface EventStoreInterface
{
    /**
     * @throws DomainEventStreamNotFoundException
     */
    public function load(Uuid $id, ?int $playhead = null): DomainEventStream;

    /**
     * @throws PlayheadAlreadyExistsException
     * @throws EventStoreException
     */
    public function append(DomainEventStream $stream): void;
}

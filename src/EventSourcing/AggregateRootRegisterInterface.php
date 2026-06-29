<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

/**
 * Registers an aggregate's uncommitted events: appends them and publishes them.
 * The write-side counterpart of AggregateRootLoaderInterface — the seam the
 * repository depends on to save, and the seam snapshotting decorates so a
 * snapshot is captured after the events are stored.
 *
 * @template-contravariant TAggregate of AggregateRootInterface
 */
interface AggregateRootRegisterInterface
{
    /**
     * @param TAggregate $aggregateRoot
     */
    public function __invoke(AggregateRootInterface $aggregateRoot): void;
}

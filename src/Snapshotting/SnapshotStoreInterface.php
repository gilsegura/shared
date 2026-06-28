<?php

declare(strict_types=1);

namespace Shared\Snapshotting;

use Shared\Domain\Uuid;
use Shared\EventSourcing\AggregateRootInterface;

/**
 * Persistence port for aggregate snapshots, keyed by aggregate id: it only
 * stores and retrieves the latest snapshot, with no policy of its own. load()
 * yields null when none was ever taken. This is to snapshots what the event
 * store is to events — the "where", not the "when" or "how".
 *
 * @template TAggregate of AggregateRootInterface
 */
interface SnapshotStoreInterface
{
    /**
     * @return Snapshot<TAggregate>|null
     */
    public function load(Uuid $id): ?Snapshot;

    /**
     * @param Snapshot<TAggregate> $snapshot
     */
    public function save(Snapshot $snapshot): void;
}

<?php

declare(strict_types=1);

namespace Shared\Tests\Snapshotting;

use Shared\Domain\Uuid;
use Shared\EventSourcing\AggregateRootInterface;
use Shared\Snapshotting\Snapshot;
use Shared\Snapshotting\SnapshotStoreInterface;

/**
 * @template TAggregate of AggregateRootInterface
 *
 * @implements SnapshotStoreInterface<TAggregate>
 */
final class InMemorySnapshotStore implements SnapshotStoreInterface
{
    /**
     * @var array<string, Snapshot<TAggregate>>
     */
    private array $snapshots = [];

    #[\Override]
    public function load(Uuid $id): ?Snapshot
    {
        return $this->snapshots[$id->uuid] ?? null;
    }

    #[\Override]
    public function save(Snapshot $snapshot): void
    {
        $this->snapshots[$snapshot->id->uuid] = $snapshot;
    }
}

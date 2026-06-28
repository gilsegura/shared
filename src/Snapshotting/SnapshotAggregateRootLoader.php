<?php

declare(strict_types=1);

namespace Shared\Snapshotting;

use Shared\Domain\Uuid;
use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;
use Shared\EventSourcing\AggregateRootInterface;
use Shared\EventSourcing\AggregateRootLoaderInterface;
use Shared\EventStore\EventStoreInterface;

/**
 * A loader that rebuilds from a snapshot when one exists — fetching and replaying
 * only the events recorded after it — and delegates to the inner loader (a full
 * rebuild) when there is none. Snapshotting as a decorator over aggregate
 * loading, composed like the upcasting event store: Snapshot(EventStore(...)).
 *
 * @template-covariant TAggregate of AggregateRootInterface
 *
 * @implements AggregateRootLoaderInterface<TAggregate>
 */
final readonly class SnapshotAggregateRootLoader implements AggregateRootLoaderInterface
{
    /**
     * @param AggregateRootLoaderInterface<TAggregate> $inner
     * @param SnapshotStoreInterface<TAggregate>       $snapshotStore
     */
    public function __construct(
        private AggregateRootLoaderInterface $inner,
        private EventStoreInterface $eventStore,
        private SnapshotStoreInterface $snapshotStore,
    ) {
    }

    /**
     * @return TAggregate
     */
    #[\Override]
    public function __invoke(Uuid $id): AggregateRootInterface
    {
        $snapshot = $this->snapshotStore->load($id);

        if (!$snapshot instanceof Snapshot) {
            return ($this->inner)($id);
        }

        $aggregateRoot = $snapshot->aggregateRoot;

        if (!$aggregateRoot instanceof AbstractEventSourcedAggregateRoot) {
            throw CorruptSnapshotException::notAnAggregateRoot($id);
        }

        // The snapshot carries the aggregate at its playhead, so only the events
        // recorded after it (playhead + 1 onward) are loaded and replayed onto
        // it, continuing from the playhead it already holds — saving both the
        // read and the replay of everything up to the snapshot.
        $aggregateRoot->initialize(
            $this->eventStore->load($id, $snapshot->playhead + 1),
        );

        /* @var TAggregate $aggregateRoot */
        return $aggregateRoot;
    }
}

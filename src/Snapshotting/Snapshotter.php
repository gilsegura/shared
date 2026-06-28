<?php

declare(strict_types=1);

namespace Shared\Snapshotting;

use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;
use Shared\EventSourcing\AggregateRootInterface;

/**
 * The write side of snapshotting: after an aggregate is saved, it asks the
 * strategy whether a snapshot is due at the current playhead and, if so, captures
 * it. The read side lives in SnapshotAggregateRootLoader; this holds no
 * rehydration logic, only the capture policy and the capture itself.
 *
 * @template TAggregate of AggregateRootInterface
 */
final readonly class Snapshotter
{
    /**
     * @param SnapshotStoreInterface<TAggregate> $snapshotStore
     */
    public function __construct(
        private SnapshotStoreInterface $snapshotStore,
        private SnapshotStrategyInterface $strategy,
    ) {
    }

    /**
     * @param TAggregate $aggregateRoot
     *
     * @throws \Throwable
     */
    public function __invoke(AggregateRootInterface $aggregateRoot): void
    {
        if (!$aggregateRoot instanceof AbstractEventSourcedAggregateRoot) {
            return;
        }

        $playhead = $aggregateRoot->playhead();

        if (!$this->strategy->shouldSnapshot($playhead)) {
            return;
        }

        /** @var Snapshot<TAggregate> $snapshot */
        $snapshot = new Snapshot($aggregateRoot->id(), $playhead, $aggregateRoot);

        $this->snapshotStore->save($snapshot);
    }
}

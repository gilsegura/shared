<?php

declare(strict_types=1);

namespace Shared\Snapshotting;

use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;
use Shared\EventSourcing\AggregateRootInterface;
use Shared\EventSourcing\AggregateRootRegisterInterface;

/**
 * A register that decorates another register: it delegates the append-and-publish
 * to the register register first, then asks the strategy whether a snapshot is due
 * at the aggregate's current playhead and, if so, captures it. The write-side
 * counterpart of SnapshotAggregateRootregister, composed the same way:
 * Snapshot(EventStore(...)). The aggregate stays unaware it was captured.
 *
 * @template-contravariant TAggregate of AggregateRootInterface
 *
 * @implements AggregateRootRegisterInterface<TAggregate>
 */
final readonly class SnapshotAggregateRootRegister implements AggregateRootRegisterInterface
{
    /**
     * @param AggregateRootRegisterInterface<TAggregate> $register
     * @param SnapshotStoreInterface<TAggregate>         $snapshotStore
     */
    public function __construct(
        private AggregateRootRegisterInterface $register,
        private SnapshotStoreInterface $snapshotStore,
        private SnapshotStrategyInterface $strategy,
    ) {
    }

    /**
     * @param TAggregate $aggregateRoot
     */
    #[\Override]
    public function __invoke(AggregateRootInterface $aggregateRoot): void
    {
        ($this->register)($aggregateRoot);

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

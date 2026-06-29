<?php

declare(strict_types=1);

namespace Shared\Tests\Snapshotting;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;
use Shared\Domain\Uuid;
use Shared\EventSourcing\AggregateRootInterface;
use Shared\EventSourcing\AggregateRootLoaderInterface;
use Shared\EventSourcing\Factory\PublicConstructorAggregateRootFactory;
use Shared\EventSourcing\Loader\EventStoreAggregateRootLoader;
use Shared\Snapshotting\CorruptSnapshotException;
use Shared\Snapshotting\Snapshot;
use Shared\Snapshotting\SnapshotAggregateRootLoader;
use Shared\Tests\EventStore\InMemoryEventStore;

final class SnapshotAggregateRootLoaderTest extends TestCase
{
    #[Test]
    public function it_delegates_to_the_inner_loader_when_there_is_no_snapshot(): void
    {
        $id = new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac');

        $eventStore = new InMemoryEventStore();
        $eventStore->append($this->streamOf($id, 0, 1, 2));

        /** @var InMemorySnapshotStore<SnapshottableAggregateRoot> $snapshots */
        $snapshots = new InMemorySnapshotStore();

        $loader = new SnapshotAggregateRootLoader(
            new EventStoreAggregateRootLoader(
                $eventStore,
                new PublicConstructorAggregateRootFactory(SnapshottableAggregateRoot::class),
            ),
            $eventStore,
            $snapshots,
        );

        $aggregateRoot = $loader($id);

        self::assertInstanceOf(SnapshottableAggregateRoot::class, $aggregateRoot);
        // full replay: three events => playhead 2
        self::assertSame(2, $aggregateRoot->playhead());
    }

    #[Test]
    public function it_resumes_from_a_snapshot_replaying_only_later_events(): void
    {
        $id = new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac');

        // The event store holds the full history (5 events, playhead 0..4).
        $eventStore = new InMemoryEventStore();
        $eventStore->append($this->streamOf($id, 0, 1, 2, 3, 4));

        // A snapshot captured at playhead 2: the aggregate already advanced to 2,
        // so only events 3 and 4 should be replayed onto it.
        $captured = new SnapshottableAggregateRoot();
        $captured->initialize($this->streamOf($id, 0, 1, 2));

        /** @var InMemorySnapshotStore<SnapshottableAggregateRoot> $snapshots */
        $snapshots = new InMemorySnapshotStore();
        $snapshots->save(new Snapshot($id, 2, $captured));

        $loader = new SnapshotAggregateRootLoader(
            new EventStoreAggregateRootLoader(
                $eventStore,
                new PublicConstructorAggregateRootFactory(SnapshottableAggregateRoot::class),
            ),
            $eventStore,
            $snapshots,
        );

        $aggregateRoot = $loader($id);

        // It must be the very object carried by the snapshot, advanced to 4.
        self::assertSame($captured, $aggregateRoot);
        self::assertSame(4, $aggregateRoot->playhead());
    }

    #[Test]
    public function it_keeps_the_snapshot_playhead_when_no_later_events_exist(): void
    {
        $id = new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac');

        // Store and snapshot are at the same position: nothing to replay.
        $eventStore = new InMemoryEventStore();
        $eventStore->append($this->streamOf($id, 0, 1, 2));

        $captured = new SnapshottableAggregateRoot();
        $captured->initialize($this->streamOf($id, 0, 1, 2));

        /** @var InMemorySnapshotStore<SnapshottableAggregateRoot> $snapshots */
        $snapshots = new InMemorySnapshotStore();
        $snapshots->save(new Snapshot($id, 2, $captured));

        $loader = new SnapshotAggregateRootLoader(
            new EventStoreAggregateRootLoader(
                $eventStore,
                new PublicConstructorAggregateRootFactory(SnapshottableAggregateRoot::class),
            ),
            $eventStore,
            $snapshots,
        );

        $aggregateRoot = $loader($id);

        self::assertSame(2, $aggregateRoot->playhead());
    }

    #[Test]
    public function it_raises_a_corrupt_snapshot_exception_when_the_snapshot_is_not_an_event_sourced_aggregate(): void
    {
        self::expectException(CorruptSnapshotException::class);

        $id = new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac');

        $eventStore = new InMemoryEventStore();
        $eventStore->append($this->streamOf($id, 0));

        /** @var InMemorySnapshotStore<AggregateRootInterface> $snapshots */
        $snapshots = new InMemorySnapshotStore();
        $snapshots->save(new Snapshot($id, 0, new NotAnEventSourcedSnapshotAggregate($id)));

        // The inner loader is never reached: the corrupt snapshot is rejected
        // before any delegation, so a stub keeps the scenario typed on the base.
        $inner = new class implements AggregateRootLoaderInterface {
            #[\Override]
            public function __invoke(Uuid $id): AggregateRootInterface
            {
                throw new \LogicException('The inner loader must not be reached.');
            }
        };

        $loader = new SnapshotAggregateRootLoader($inner, $eventStore, $snapshots);

        $loader($id);
    }

    private function streamOf(Uuid $id, int ...$playheads): DomainEventStream
    {
        $messages = \array_map(
            static fn (int $playhead): DomainMessage => DomainMessage::record(
                $id,
                $playhead,
                Metadata::empty(),
                new SnapshottableAggregateRootWasTouched($id),
            ),
            $playheads,
        );

        return new DomainEventStream(...$messages);
    }
}

/**
 * Implements the aggregate contract but is not event-sourced, so a snapshot
 * carrying it must be rejected as corrupt.
 */
final class NotAnEventSourcedSnapshotAggregate implements AggregateRootInterface
{
    public function __construct(
        private readonly Uuid $id,
    ) {
    }

    #[\Override]
    public function id(): Uuid
    {
        return $this->id;
    }

    #[\Override]
    public function uncommittedEvents(): DomainEventStream
    {
        return new DomainEventStream();
    }
}

<?php

declare(strict_types=1);

namespace Shared\Tests\Snapshotting;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;
use Shared\Domain\Uuid;
use Shared\Snapshotting\EventCountSnapshotStrategy;
use Shared\Snapshotting\Snapshotter;

final class SnapshotterTest extends TestCase
{
    private const string ID = '9db0db88-3e44-4d2b-b46f-9ca547de06ac';

    #[Test]
    public function it_captures_a_snapshot_when_the_strategy_says_so(): void
    {
        $id = new Uuid(self::ID);

        $aggregateRoot = new SnapshottableAggregateRoot();
        // advance to playhead 2 (three events), with every = 3 => boundary
        $aggregateRoot->initialize($this->streamOf($id, 0, 1, 2));

        /** @var InMemorySnapshotStore<SnapshottableAggregateRoot> $snapshots */
        $snapshots = new InMemorySnapshotStore();

        $snapshotter = new Snapshotter($snapshots, new EventCountSnapshotStrategy(3));
        $snapshotter($aggregateRoot);

        $snapshot = $snapshots->load($id);

        self::assertNotNull($snapshot);
        self::assertSame(2, $snapshot->playhead);
        self::assertSame($aggregateRoot, $snapshot->aggregateRoot);
    }

    #[Test]
    public function it_does_not_capture_between_boundaries(): void
    {
        $id = new Uuid(self::ID);

        $aggregateRoot = new SnapshottableAggregateRoot();
        $aggregateRoot->initialize($this->streamOf($id, 0, 1)); // playhead 1, every 3

        /** @var InMemorySnapshotStore<SnapshottableAggregateRoot> $snapshots */
        $snapshots = new InMemorySnapshotStore();

        new Snapshotter($snapshots, new EventCountSnapshotStrategy(3))($aggregateRoot);

        self::assertNull($snapshots->load($id));
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

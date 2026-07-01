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
use Shared\EventSourcing\AggregateRootRegisterInterface;
use Shared\Snapshotting\EventCountSnapshotStrategy;
use Shared\Snapshotting\SnapshotAggregateRootRegister;

final class SnapshotAggregateRootRegisterTest extends TestCase
{
    private const string ID = '9db0db88-3e44-4d2b-b46f-9ca547de06ac';

    #[Test]
    public function it_delegates_to_the_inner_register(): void
    {
        $inner = new RecordingRegister();

        /** @var InMemorySnapshotStore<SnapshottableAggregateRoot> $snapshots */
        $snapshots = new InMemorySnapshotStore();

        $register = new SnapshotAggregateRootRegister($inner, $snapshots, new EventCountSnapshotStrategy(3));

        $aggregateRoot = $this->aggregateAt(0, 1);

        $register($aggregateRoot);

        self::assertSame($aggregateRoot, $inner->registered);
    }

    #[Test]
    public function it_captures_a_snapshot_when_the_strategy_says_so(): void
    {
        /** @var InMemorySnapshotStore<SnapshottableAggregateRoot> $snapshots */
        $snapshots = new InMemorySnapshotStore();

        $register = new SnapshotAggregateRootRegister(new RecordingRegister(), $snapshots, new EventCountSnapshotStrategy(3));

        // Three events => playhead 2, every 3 => boundary.
        $aggregateRoot = $this->aggregateAt(0, 1, 2);

        $register($aggregateRoot);

        $snapshot = $snapshots->load(new Uuid(self::ID));

        self::assertNotNull($snapshot);
        self::assertSame(2, $snapshot->playhead);
        self::assertSame($aggregateRoot, $snapshot->aggregateRoot);
    }

    #[Test]
    public function it_does_not_capture_between_boundaries(): void
    {
        /** @var InMemorySnapshotStore<SnapshottableAggregateRoot> $snapshots */
        $snapshots = new InMemorySnapshotStore();

        $register = new SnapshotAggregateRootRegister(new RecordingRegister(), $snapshots, new EventCountSnapshotStrategy(3));

        $register($this->aggregateAt(0, 1)); // playhead 1

        self::assertNull($snapshots->load(new Uuid(self::ID)));
    }

    private function aggregateAt(int ...$playheads): SnapshottableAggregateRoot
    {
        $id = new Uuid(self::ID);

        $messages = \array_map(
            static fn (int $playhead): DomainMessage => DomainMessage::record(
                $id,
                $playhead,
                Metadata::empty(),
                new SnapshottableAggregateRootWasTouched($id),
            ),
            $playheads,
        );

        $aggregateRoot = new SnapshottableAggregateRoot();
        $aggregateRoot->initialize(new DomainEventStream(...$messages));

        return $aggregateRoot;
    }
}

/**
 * @implements AggregateRootRegisterInterface<SnapshottableAggregateRoot>
 */
final class RecordingRegister implements AggregateRootRegisterInterface
{
    public ?AggregateRootInterface $registered = null;

    #[\Override]
    public function __invoke(AggregateRootInterface $aggregateRoot): void
    {
        $this->registered = $aggregateRoot;
    }
}

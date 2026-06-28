<?php

declare(strict_types=1);

namespace Shared\Tests\Snapshotting;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shared\Snapshotting\EventCountSnapshotStrategy;

final class EventCountSnapshotStrategyTest extends TestCase
{
    #[Test]
    public function it_snapshots_on_each_multiple_boundary(): void
    {
        $strategy = new EventCountSnapshotStrategy(100);

        // playhead is 0-indexed: the 100th event sits at playhead 99.
        self::assertTrue($strategy->shouldSnapshot(99));
        self::assertTrue($strategy->shouldSnapshot(199));
    }

    #[Test]
    public function it_does_not_snapshot_between_boundaries(): void
    {
        $strategy = new EventCountSnapshotStrategy(100);

        self::assertFalse($strategy->shouldSnapshot(0));
        self::assertFalse($strategy->shouldSnapshot(98));
        self::assertFalse($strategy->shouldSnapshot(100));
    }

    #[Test]
    public function it_never_snapshots_for_a_negative_playhead(): void
    {
        self::assertFalse(new EventCountSnapshotStrategy(100)->shouldSnapshot(-1));
    }

    #[Test]
    public function it_is_disabled_for_a_non_positive_every(): void
    {
        self::assertFalse(new EventCountSnapshotStrategy(0)->shouldSnapshot(99));
        self::assertFalse(new EventCountSnapshotStrategy(-5)->shouldSnapshot(99));
    }
}

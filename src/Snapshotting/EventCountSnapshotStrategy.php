<?php

declare(strict_types=1);

namespace Shared\Snapshotting;

/**
 * Snapshots each time the aggregate crosses a multiple of $every events, i.e.
 * when its playhead (0-indexed) sits on a boundary. An $every of 0 or less
 * disables snapshotting.
 */
final readonly class EventCountSnapshotStrategy implements SnapshotStrategyInterface
{
    public function __construct(
        private int $every,
    ) {
    }

    #[\Override]
    public function shouldSnapshot(int $playhead): bool
    {
        return $this->every > 0
            && $playhead >= 0
            && 0 === ($playhead + 1) % $this->every;
    }
}

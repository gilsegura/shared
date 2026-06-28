<?php

declare(strict_types=1);

namespace Shared\Snapshotting;

/**
 * Decides whether a snapshot should be taken at the aggregate's current
 * playhead. Implementations encode the policy — every N events, never — keeping
 * the Snapshotter free of any particular rule.
 */
interface SnapshotStrategyInterface
{
    /**
     * @param int $playhead the aggregate's current playhead
     */
    public function shouldSnapshot(int $playhead): bool;
}

<?php

declare(strict_types=1);

namespace Shared\Snapshotting;

use Shared\Domain\Uuid;
use Shared\EventSourcing\AggregateRootInterface;

/**
 * A point-in-time capture of an aggregate at a given playhead, so it can be
 * rebuilt from the snapshot plus only the events recorded after it, instead of
 * replaying the whole stream. The aggregate is unaware it was captured.
 *
 * Read-only, so it is covariant in TAggregate: a Snapshot<Thing> is usable as a
 * Snapshot<AggregateRootInterface>. TAggregate only appears in the read position
 * of the public $aggregateRoot property.
 *
 * @template-covariant TAggregate of AggregateRootInterface
 */
final readonly class Snapshot
{
    /**
     * @param TAggregate $aggregateRoot
     */
    public function __construct(
        public Uuid $id,
        public int $playhead,
        public AggregateRootInterface $aggregateRoot,
    ) {
    }
}

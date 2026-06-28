<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\Uuid;

/**
 * Loads a fully rebuilt aggregate by its id. Unlike a factory — which only turns
 * a given stream into an aggregate — a loader is responsible for fetching what
 * it needs (events, snapshots) and producing the aggregate, so taking the id is
 * its job. This is the seam the repository depends on, and the seam snapshotting
 * decorates.
 *
 * @template-covariant TAggregate of AggregateRootInterface
 */
interface AggregateRootLoaderInterface
{
    /**
     * @return TAggregate
     */
    public function __invoke(Uuid $id): AggregateRootInterface;
}

<?php

declare(strict_types=1);

namespace Shared\Replaying;

use Shared\Criteria;
use Shared\EventStore\EventStoreManagerInterface;
use Shared\EventStore\EventVisitorInterface;

/**
 * Visits the events matching a criteria from the store and feeds them to
 * an event visitor, e.g. to rebuild a read model from history.
 */
final readonly class Replayer
{
    public function __construct(
        private EventStoreManagerInterface $eventStore,
        private EventVisitorInterface $eventVisitor,
    ) {
    }

    public function __invoke(Criteria\AndX|Criteria\OrX $criteria): void
    {
        $this->eventStore->visitEvents($criteria, $this->eventVisitor);
    }
}

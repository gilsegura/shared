<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Criteria;

/**
 * Visits events across the store for replay and maintenance.
 */
interface EventStoreManagerInterface
{
    /**
     * @throws EventStoreException
     */
    public function visitEvents(Criteria\AndX|Criteria\OrX $criteria, EventVisitorInterface $eventVisitor): void;
}

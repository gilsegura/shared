<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventStream;

/**
 * Rebuilds an aggregate instance from a domain event stream.
 *
 * @template-covariant TAggregate of AggregateRootInterface
 */
interface AggregateRootFactoryInterface
{
    /**
     * @return TAggregate
     */
    public function __invoke(DomainEventStream $stream): AggregateRootInterface;
}

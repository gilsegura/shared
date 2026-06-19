<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventStream;

/**
 * Rebuilds an aggregate instance from a domain event stream.
 */
interface AggregateRootFactoryInterface
{
    public function __invoke(DomainEventStream $stream): AggregateRootInterface;
}

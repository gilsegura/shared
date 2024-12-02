<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventStream;

interface AggregateRootFactoryInterface
{
    public function __invoke(DomainEventStream $stream): AggregateRootInterface;
}

<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventStream;

interface EventStreamDecoratorInterface
{
    public function decorate(DomainEventStream $stream): DomainEventStream;
}

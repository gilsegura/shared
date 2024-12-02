<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventStream;

interface EventStreamDecoratorInterface
{
    public function __invoke(DomainEventStream $stream): DomainEventStream;
}

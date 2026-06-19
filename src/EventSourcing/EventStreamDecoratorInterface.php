<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventStream;

/**
 * Decorates an outgoing event stream before it is appended, e.g. to enrich
 * metadata.
 */
interface EventStreamDecoratorInterface
{
    public function __invoke(DomainEventStream $stream): DomainEventStream;
}

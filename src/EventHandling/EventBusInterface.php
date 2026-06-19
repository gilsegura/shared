<?php

declare(strict_types=1);

namespace Shared\EventHandling;

use Shared\Domain\DomainEventStream;

/**
 * Publishes a domain event stream to its listeners.
 */
interface EventBusInterface
{
    /**
     * @throws EventBusException
     */
    public function __invoke(DomainEventStream $stream): void;
}

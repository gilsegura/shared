<?php

declare(strict_types=1);

namespace Shared\EventHandling;

use Shared\Domain\DomainEventStream;

interface EventBusInterface
{
    /**
     * @throws EventBusException
     */
    public function __invoke(DomainEventStream $stream): void;
}

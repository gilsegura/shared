<?php

declare(strict_types=1);

namespace Shared\EventHandling;

use Shared\Domain\DomainEventStream;

interface EventBusInterface
{
    public function subscribe(EventListenerInterface $eventListener): void;

    /**
     * @throws EventBusException
     */
    public function publish(DomainEventStream $stream): void;
}

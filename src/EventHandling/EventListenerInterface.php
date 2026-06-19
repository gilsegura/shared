<?php

declare(strict_types=1);

namespace Shared\EventHandling;

use Shared\Domain\DomainMessage;

/**
 * Listens to domain messages published on the event bus.
 */
interface EventListenerInterface
{
    public function __invoke(DomainMessage $message): void;
}

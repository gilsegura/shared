<?php

declare(strict_types=1);

namespace Shared\EventHandling;

use Shared\Domain\DomainMessage;

interface EventListenerInterface
{
    /**
     * @throws \Throwable
     */
    public function handle(DomainMessage $message): void;
}

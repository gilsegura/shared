<?php

declare(strict_types=1);

namespace Shared\EventHandling;

use Shared\Domain\DomainMessage;

interface EventListenerInterface
{
    public function __invoke(DomainMessage $message): void;
}

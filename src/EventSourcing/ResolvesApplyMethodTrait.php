<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventInterface;

trait ResolvesApplyMethodTrait
{
    private function applyMethod(DomainEventInterface $event): string
    {
        $parts = explode('\\', $event::class);

        return 'apply'.end($parts);
    }
}

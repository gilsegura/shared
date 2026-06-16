<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventInterface;
use Shared\Support\ClassName;

trait ResolvesApplyMethodTrait
{
    private function applyMethod(DomainEventInterface $event): string
    {
        return 'apply'.ClassName::short($event::class);
    }
}

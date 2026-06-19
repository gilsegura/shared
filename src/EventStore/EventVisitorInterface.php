<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\DomainMessage;

/**
 * Receives each domain message visited during a replay.
 */
interface EventVisitorInterface
{
    public function __invoke(DomainMessage $message): void;
}

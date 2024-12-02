<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventStream;
use Shared\Domain\IdentifiableInterface;

interface AggregateRootInterface extends IdentifiableInterface
{
    public function uncommittedEvents(): DomainEventStream;
}

<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\Uuid;
use Shared\Exception\NotFoundException;

final class DomainEventStreamNotFoundException extends NotFoundException
{
    public static function new(Uuid $id, ?int $playhead = null): self
    {
        if (null !== $playhead) {
            return new self(sprintf('DomainEventStream for AggregateRoot with id "%s" and playhead "%s" not found.', $id->uuid, $playhead));
        }

        return new self(sprintf('DomainEventStream for AggregateRoot with id "%s" not found.', $id->uuid));
    }
}

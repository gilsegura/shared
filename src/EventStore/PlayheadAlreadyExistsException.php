<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\Uuid;
use Shared\Exception\AlreadyExistsException;

final class PlayheadAlreadyExistsException extends AlreadyExistsException
{
    public static function new(Uuid $id, int $playhead): self
    {
        return new self(sprintf('DomainEventStream for AggregateRoot with id "%s" and playhead "%s" already exists.', $id->uuid, $playhead));
    }
}

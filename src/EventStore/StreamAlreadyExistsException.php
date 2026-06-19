<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\Uuid;
use Shared\Exception\ConflictException;

/**
 * Raised when appending a stream whose first event would collide with an
 * existing one.
 */
final class StreamAlreadyExistsException extends ConflictException
{
    public static function playhead(Uuid $id, int $playhead): self
    {
        return new self(sprintf('The requested stream "%s", playhead "%s" already exists.', $id->uuid, $playhead));
    }
}

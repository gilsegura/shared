<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\Uuid;
use Shared\Exception\NotFoundException;

/**
 * Raised when loading a stream that does not exist.
 */
final class StreamNotFoundException extends NotFoundException
{
    public static function id(Uuid $id): self
    {
        return new self(sprintf('The requested stream "%s" could not be found.', $id->uuid));
    }

    public static function playhead(Uuid $id, int $playhead): self
    {
        return new self(sprintf('The requested stream "%s", playhead "%s" could not be found.', $id->uuid, $playhead));
    }
}

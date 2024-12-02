<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\DomainException;
use Shared\Domain\Uuid;

final class StreamAlreadyExistsException extends DomainException
{
    public static function playhead(Uuid $id, int $playhead): self
    {
        return new self(sprintf('The requested stream "%s", playhead "%s" already exists.', $id->uuid, $playhead));
    }
}

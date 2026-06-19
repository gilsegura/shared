<?php

declare(strict_types=1);

namespace Shared\Upcasting;

use Shared\Domain\DomainMessage;

/**
 * Transforms a stored domain message into a newer event shape. An upcaster
 * returns the message unchanged when the event is not its concern, or a new
 * DomainMessage carrying the upgraded payload when it is, keeping the id,
 * playhead, metadata and recorded time. Upcasting happens on read, never
 * rewriting what is persisted.
 */
interface UpcasterInterface
{
    public function __invoke(DomainMessage $message): DomainMessage;
}

<?php

declare(strict_types=1);

namespace Shared\Tests\Upcasting;

use Shared\Domain\DomainMessage;
use Shared\Upcasting\UpcasterInterface;

/**
 * Upcasts EventV2WasOccurred to EventV3WasOccurred, leaving any other event
 * untouched. Used to prove a chain feeds each upcaster the previous output.
 */
final readonly class EventWasOccurredV2ToV3Upcaster implements UpcasterInterface
{
    #[\Override]
    public function __invoke(DomainMessage $message): DomainMessage
    {
        $event = $message->payload;

        if ($event instanceof EventV2WasOccurred) {
            return new DomainMessage(
                $message->id,
                $message->playhead,
                $message->metadata,
                new EventV3WasOccurred(),
                $message->recordedAt
            );
        }

        return $message;
    }
}

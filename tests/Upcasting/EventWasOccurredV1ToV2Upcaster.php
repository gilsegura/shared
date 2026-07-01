<?php

declare(strict_types=1);

namespace Shared\Tests\Upcasting;

use Shared\Domain\DomainMessage;
use Shared\Upcasting\UpcasterInterface;

/**
 * Upcasts EventV1WasOccurred to EventV2WasOccurred, leaving any other event
 * untouched. Fixture for the upcasting tests.
 */
final readonly class EventWasOccurredV1ToV2Upcaster implements UpcasterInterface
{
    #[\Override]
    public function __invoke(DomainMessage $message): DomainMessage
    {
        $event = $message->payload;

        if (!$event instanceof EventV1WasOccurred) {
            return $message;
        }

        return new DomainMessage(
            $message->id,
            $message->playhead,
            $message->metadata,
            new EventV2WasOccurred(),
            $message->recordedAt
        );
    }
}

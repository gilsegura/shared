<?php

declare(strict_types=1);

namespace Shared\Domain;

/**
 * Wraps a domain event (payload) with its aggregate id, playhead, metadata
 * and recorded time. The unit the event store persists and the buses
 * carry.
 */
final readonly class DomainMessage
{
    public string $type;

    public function __construct(
        public Uuid $id,
        public int $playhead,
        public Metadata $metadata,
        public DomainEventInterface $payload,
        public DateTimeImmutable $recordedAt,
    ) {
        $this->type = strtr($payload::class, '\\', '.');
    }

    public static function record(
        Uuid $id,
        int $playhead,
        Metadata $metadata,
        DomainEventInterface $payload,
    ): self {
        return new self(
            $id,
            $playhead,
            $metadata,
            $payload,
            DateTimeImmutable::now(),
        );
    }

    public function addMetadata(Metadata $metadata): self
    {
        return new self(
            $this->id,
            $this->playhead,
            $this->metadata->merge($metadata),
            $this->payload,
            $this->recordedAt,
        );
    }
}

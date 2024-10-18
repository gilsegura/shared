<?php

declare(strict_types=1);

namespace Shared\Domain;

final readonly class DomainMessage
{
    public string $type;

    public function __construct(
        public Uuid $id,
        public int $playhead,
        public Metadata $metadata,
        public DomainEventInterface $payload,
        public HighResolutionTimeImmutable $recordedAt,
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
            HighResolutionTimeImmutable::now()
        );
    }

    public function addMetadata(Metadata $metadata): self
    {
        return new self(
            $this->id,
            $this->playhead,
            $this->metadata->merge($metadata),
            $this->payload,
            $this->recordedAt
        );
    }
}

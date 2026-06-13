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
        return clone ($this, [
            'metadata' => $this->metadata->merge($metadata),
        ]);
    }
}

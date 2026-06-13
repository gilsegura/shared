<?php

declare(strict_types=1);

namespace Shared\Domain;

use Serializer\SerializableInterface;

/**
 * @implements SerializableInterface<array<array-key, mixed>>
 */
final readonly class Metadata implements SerializableInterface
{
    /**
     * @param array<array-key, mixed> $metadata
     */
    private function __construct(
        public array $metadata,
    ) {
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public static function kv(string $key, mixed $value): self
    {
        return new self([$key => $value]);
    }

    public function merge(Metadata $metadata): self
    {
        return clone ($this, [
            'metadata' => [...$this->metadata, ...$metadata->metadata],
        ]);
    }

    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self($attributes);
    }

    #[\Override]
    public function serialize(): array
    {
        return $this->metadata;
    }
}

<?php

declare(strict_types=1);

namespace Shared\Domain;

use Serializer\SerializableInterface;

final readonly class Metadata implements SerializableInterface
{
    /**
     * @param array<array-key, mixed> $values
     */
    private function __construct(
        public array $values,
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
        return new self([...$this->values, ...$metadata->values]);
    }

    /**
     * @param array<array-key, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self($attributes);
    }

    /**
     * @return array<array-key, mixed>
     */
    #[\Override]
    public function serialize(): array
    {
        return $this->values;
    }
}

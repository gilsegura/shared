<?php

declare(strict_types=1);

namespace Shared\Domain;

use Serializer\SerializableInterface;

final readonly class Metadata implements SerializableInterface
{
    /**
     * @param array<string, mixed> $values
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
        return new self(array_merge($this->values, $metadata->values));
    }

    /**
     * @param array<string, mixed> $data
     */
    #[\Override]
    public static function deserialize(array $data): static
    {
        return new self($data);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function serialize(): array
    {
        return $this->values;
    }
}

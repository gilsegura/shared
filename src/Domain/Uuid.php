<?php

declare(strict_types=1);

namespace Shared\Domain;

use Ramsey\Uuid\Uuid as Generator;

final readonly class Uuid
{
    public function __construct(
        public string $uuid,
    ) {
        if (!Generator::isValid($this->uuid)) {
            throw new \InvalidArgumentException('Value must be a valid UUID.');
        }
    }

    public function equals(Uuid $uuid): bool
    {
        return $this->uuid === $uuid->uuid;
    }

    public static function uuid4(): self
    {
        return new self(Generator::uuid4()->toString());
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->uuid;
    }
}

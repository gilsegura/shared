<?php

declare(strict_types=1);

namespace Shared\Domain;

use Ramsey\Uuid\Uuid as Generator;

final readonly class Uuid
{
    public string $uuid;

    public function __construct(
        string $uuid,
    ) {
        if (1 !== preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/iD', $uuid)) {
            throw new \InvalidArgumentException('Value must be a valid UUID.');
        }

        $this->uuid = $uuid;
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

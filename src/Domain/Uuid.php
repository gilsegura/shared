<?php

declare(strict_types=1);

namespace Shared\Domain;

use ProxyAssert\Assertion;
use Ramsey\Uuid\Uuid as Generator;

final readonly class Uuid
{
    public string $uuid;

    public function __construct(
        string $uuid,
    ) {
        Assertion::uuid($uuid);

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

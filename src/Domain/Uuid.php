<?php

declare(strict_types=1);

namespace Shared\Domain;

use Ramsey\Uuid\Uuid as Generator;
use Shared\Exception\InvalidInputException;

/**
 * A UUID value object with validation and equality.
 */
final readonly class Uuid
{
    public function __construct(
        public string $uuid,
    ) {
        if (!Generator::isValid($this->uuid)) {
            throw InvalidInputException::notAValidUuid($this->uuid);
        }
    }

    public static function uuid4(): self
    {
        return new self(Generator::uuid4()->toString());
    }

    public function equals(Uuid $uuid): bool
    {
        return $this->uuid === $uuid->uuid;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->uuid;
    }
}

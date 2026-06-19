<?php

declare(strict_types=1);

namespace Shared\Domain;

use Shared\Exception\InvalidInputException;

/**
 * A string value object guaranteed to be non-empty.
 */
final readonly class NotEmptyString
{
    public function __construct(
        public string $string,
    ) {
        if ('' === $this->string) {
            throw InvalidInputException::emptyString();
        }
    }

    public function equals(NotEmptyString $string): bool
    {
        return $this->string === $string->string;
    }
}

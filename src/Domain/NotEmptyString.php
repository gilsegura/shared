<?php

declare(strict_types=1);

namespace Shared\Domain;

/**
 * A string value object guaranteed to be non-empty.
 */
final readonly class NotEmptyString
{
    public function __construct(
        public string $string,
    ) {
        if ('' === $this->string) {
            throw new \InvalidArgumentException('The value must not be an empty string.');
        }
    }

    public function equals(NotEmptyString $string): bool
    {
        return $this->string === $string->string;
    }
}

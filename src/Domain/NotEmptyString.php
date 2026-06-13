<?php

declare(strict_types=1);

namespace Shared\Domain;

final readonly class NotEmptyString
{
    public function __construct(
        public string $string,
    ) {
        if ('' === $this->string) {
            throw new \InvalidArgumentException('Value must not be empty.');
        }
    }

    public function equals(NotEmptyString $string): bool
    {
        return $this->string === $string->string;
    }
}

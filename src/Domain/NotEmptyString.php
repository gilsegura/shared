<?php

declare(strict_types=1);

namespace Shared\Domain;

use Shared\Exception\InvalidInputException;

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

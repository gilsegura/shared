<?php

declare(strict_types=1);

namespace Shared\Domain;

final readonly class NotEmptyString
{
    public string $string;

    public function __construct(
        string $string,
    ) {
        if ('' === $string) {
            throw new \InvalidArgumentException('Value must not be empty.');
        }

        $this->string = $string;
    }

    public function equals(NotEmptyString $string): bool
    {
        return $this->string === $string->string;
    }
}

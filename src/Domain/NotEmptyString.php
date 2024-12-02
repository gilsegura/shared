<?php

declare(strict_types=1);

namespace Shared\Domain;

use ProxyAssert\Assertion;

final readonly class NotEmptyString
{
    public string $string;

    public function __construct(
        string $string,
    ) {
        Assertion::notEmpty($string);

        $this->string = $string;
    }

    public function equals(NotEmptyString $string): bool
    {
        return $this->string === $string->string;
    }
}

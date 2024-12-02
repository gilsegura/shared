<?php

declare(strict_types=1);

namespace Shared\Domain;

use ProxyAssert\Assertion;

final readonly class HighResolutionTimeImmutable
{
    public int $time;

    public function __construct(
        int $time,
    ) {
        Assertion::between($time, PHP_INT_MIN, PHP_INT_MAX);

        $this->time = $time;
    }

    public static function now(): self
    {
        return new self(hrtime(true));
    }

    public function equals(HighResolutionTimeImmutable $time): bool
    {
        return $this->time === $time->time;
    }
}

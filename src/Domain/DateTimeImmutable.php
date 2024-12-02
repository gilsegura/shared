<?php

declare(strict_types=1);

namespace Shared\Domain;

use ProxyAssert\Assertion;

final readonly class DateTimeImmutable
{
    public string $dateTime;

    public function __construct(
        string $dateTime,
    ) {
        Assertion::date($dateTime, DATE_ATOM);

        $this->dateTime = $dateTime;
    }

    public static function now(): self
    {
        return new self(date(DATE_ATOM));
    }

    public function equals(DateTimeImmutable $dateTime): bool
    {
        return $this->dateTime === $dateTime->dateTime;
    }

    public function gt(DateTimeImmutable $dateTime): bool
    {
        return $this->dateTime > $dateTime->dateTime;
    }
}

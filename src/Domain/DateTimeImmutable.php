<?php

declare(strict_types=1);

namespace Shared\Domain;

final readonly class DateTimeImmutable
{
    public string $dateTime;

    public function __construct(
        string $dateTime,
    ) {
        $parsed = \DateTimeImmutable::createFromFormat(DATE_ATOM, $dateTime);

        if (false === $parsed || $parsed->format(DATE_ATOM) !== $dateTime) {
            throw new \InvalidArgumentException('Value must be a valid date in DATE_ATOM format.');
        }

        $this->dateTime = $parsed
            ->setTimezone(new \DateTimeZone('UTC'))
            ->format(DATE_ATOM);
    }

    public static function now(): self
    {
        return self::fromTimestamp(time());
    }

    public static function fromTimestamp(int $timestamp): self
    {
        return new self(
            new \DateTimeImmutable('@'.$timestamp)
                ->setTimezone(new \DateTimeZone('UTC'))
                ->format(DATE_ATOM),
        );
    }

    public function toTimestamp(): int
    {
        return new \DateTimeImmutable($this->dateTime)->getTimestamp();
    }

    public function addSeconds(int $seconds): self
    {
        return self::fromTimestamp($this->toTimestamp() + $seconds);
    }

    public function subSeconds(int $seconds): self
    {
        return self::fromTimestamp($this->toTimestamp() - $seconds);
    }

    public function equals(DateTimeImmutable $dateTime): bool
    {
        return $this->toTimestamp() === $dateTime->toTimestamp();
    }

    public function gt(DateTimeImmutable $dateTime): bool
    {
        return $this->toTimestamp() > $dateTime->toTimestamp();
    }

    public function gte(DateTimeImmutable $dateTime): bool
    {
        return $this->toTimestamp() >= $dateTime->toTimestamp();
    }

    public function lt(DateTimeImmutable $dateTime): bool
    {
        return $this->toTimestamp() < $dateTime->toTimestamp();
    }

    public function lte(DateTimeImmutable $dateTime): bool
    {
        return $this->toTimestamp() <= $dateTime->toTimestamp();
    }
}

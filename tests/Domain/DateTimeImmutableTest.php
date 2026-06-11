<?php

declare(strict_types=1);

namespace Shared\Tests\Domain;

use PHPUnit\Framework\TestCase;
use Shared\Domain\DateTimeImmutable;

final class DateTimeImmutableTest extends TestCase
{
    public function test_must_throw_invalid_argument_exception(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new DateTimeImmutable('date');
    }

    public function test_must_normalize_to_utc(): void
    {
        $dateTime = new DateTimeImmutable('2024-01-01T01:00:00+01:00');

        self::assertSame('2024-01-01T00:00:00+00:00', $dateTime->dateTime);
    }

    public function test_must_compare_equal_instants_in_different_offsets(): void
    {
        $some = new DateTimeImmutable('2024-01-01T01:00:00+01:00');
        $another = new DateTimeImmutable('2024-01-01T00:00:00+00:00');

        self::assertTrue($some->equals($another));
    }

    public function test_must_compare_ordering_by_instant(): void
    {
        $earlier = new DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $later = new DateTimeImmutable('2024-01-01T01:00:00+00:00');

        self::assertTrue($later->gt($earlier));
        self::assertTrue($later->gte($earlier));
        self::assertTrue($earlier->lt($later));
        self::assertTrue($earlier->lte($later));
        self::assertFalse($earlier->gt($later));
    }

    public function test_must_add_seconds(): void
    {
        $dateTime = new DateTimeImmutable('2024-01-01T00:00:00+00:00');

        $result = $dateTime->addSeconds(3600);

        self::assertSame('2024-01-01T01:00:00+00:00', $result->dateTime);
    }

    public function test_must_subtract_seconds(): void
    {
        $dateTime = new DateTimeImmutable('2024-01-01T01:00:00+00:00');

        $result = $dateTime->subSeconds(3600);

        self::assertSame('2024-01-01T00:00:00+00:00', $result->dateTime);
    }

    public function test_must_convert_to_timestamp(): void
    {
        $dateTime = new DateTimeImmutable('2024-01-01T00:00:00+00:00');

        self::assertSame(1704067200, $dateTime->toTimestamp());
    }
}

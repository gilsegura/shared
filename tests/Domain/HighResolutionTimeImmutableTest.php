<?php

declare(strict_types=1);

namespace Shared\Tests\Domain;

use PHPUnit\Framework\TestCase;
use Shared\Domain\HighResolutionTimeImmutable;

final class HighResolutionTimeImmutableTest extends TestCase
{
    public function test_must_compare_two_instances(): void
    {
        $some = new HighResolutionTimeImmutable(hrtime(true));
        $another = new HighResolutionTimeImmutable(hrtime(true));

        self::assertFalse($some->equals($another));
    }
}

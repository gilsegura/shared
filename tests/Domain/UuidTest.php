<?php

declare(strict_types=1);

namespace Shared\Tests\Domain;

use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Shared\Domain\Uuid;

final class UuidTest extends TestCase
{
    public function test_must_throw_assertion_failed_exception(): void
    {
        self::expectException(AssertionFailedException::class);

        new Uuid('1');
    }

    public function test_must_compare_two_instances(): void
    {
        $some = new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac');
        $another = Uuid::uuid4();

        self::assertFalse($some->equals($another));
        self::assertSame('9db0db88-3e44-4d2b-b46f-9ca547de06ac', $some->__toString());
    }
}

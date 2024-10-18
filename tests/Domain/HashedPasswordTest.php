<?php

declare(strict_types=1);

namespace Shared\Tests\Domain;

use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Shared\Domain\HashedPassword;

final class HashedPasswordTest extends TestCase
{
    public function test_must_throw_assertion_failed_exception(): void
    {
        self::expectException(AssertionFailedException::class);

        HashedPassword::encode('12345');
    }

    public function test_must_compare_two_instances(): void
    {
        $some = HashedPassword::encode('somePlainPassword');
        $another = HashedPassword::encode('anotherPlainPassword');

        self::assertFalse($some->equals($another));
    }

    public function test_must_not_match_plain_password(): void
    {
        $some = HashedPassword::encode('somePlainPassword');

        self::assertFalse($some->match('anotherPlainPassword'));
    }
}

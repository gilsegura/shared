<?php

declare(strict_types=1);

namespace Shared\Tests\Domain;

use PHPUnit\Framework\TestCase;
use ProxyAssert\Exception\AssertionFailedException;
use Shared\Domain\NotEmptyString;

final class NotEmptyStringTest extends TestCase
{
    public function test_must_throw_assertion_failed_exception(): void
    {
        self::expectException(AssertionFailedException::class);

        new NotEmptyString('');
    }

    public function test_must_compare_two_instances(): void
    {
        $some = new NotEmptyString('some');
        $another = new NotEmptyString('another');

        self::assertFalse($some->equals($another));
    }
}

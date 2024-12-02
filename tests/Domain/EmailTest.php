<?php

declare(strict_types=1);

namespace Shared\Tests\Domain;

use PHPUnit\Framework\TestCase;
use ProxyAssert\Exception\AssertionFailedException;
use Shared\Domain\Email;

final class EmailTest extends TestCase
{
    public function test_must_throw_assertion_failed_exception(): void
    {
        self::expectException(AssertionFailedException::class);

        new Email('faker');
    }

    public function test_must_compare_two_instances(): void
    {
        $some = new Email('some@email.com');
        $another = new Email('anthoer@email.com');

        self::assertFalse($some->equals($another));
    }
}

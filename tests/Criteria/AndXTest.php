<?php

declare(strict_types=1);

namespace Shared\Tests\Criteria;

use PHPUnit\Framework\TestCase;
use Shared\Criteria\AndX;
use Shared\Criteria\EqId;
use Shared\Criteria\EqPlayhead;
use Shared\Criteria\Expr;
use Shared\Domain\Uuid;

final class AndXTest extends TestCase
{
    public function test_must_compose_and_x_expression(): void
    {
        $andX = new AndX(new EqId(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac')), new EqPlayhead(1));

        self::assertInstanceOf(Expr\AndX::class, $andX->expr());
    }
}

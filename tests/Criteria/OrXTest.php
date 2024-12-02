<?php

declare(strict_types=1);

namespace Shared\Tests\Criteria;

use PHPUnit\Framework\TestCase;
use Shared\Criteria\EqId;
use Shared\Criteria\EqPlayhead;
use Shared\Criteria\Expr;
use Shared\Criteria\OrX;
use Shared\Domain\Uuid;

final class OrXTest extends TestCase
{
    public function test_must_compose_or_x_expression(): void
    {
        $orX = new OrX(new EqId(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac')), new EqPlayhead(1));

        self::assertInstanceOf(Expr\OrX::class, $orX->expr());
    }
}

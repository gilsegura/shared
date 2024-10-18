<?php

declare(strict_types=1);

namespace Shared\Tests\Criteria;

use PHPUnit\Framework\TestCase;
use Shared\Criteria\ByPlayhead;
use Shared\Criteria\Expr;
use Shared\Criteria\OrderX;

final class OrderXTest extends TestCase
{
    public function test_must_compose_order_x_expression(): void
    {
        $andX = new OrderX(new ByPlayhead(Expr\Order::ASC));

        self::assertInstanceOf(Expr\OrderX::class, $andX->expr());
    }
}

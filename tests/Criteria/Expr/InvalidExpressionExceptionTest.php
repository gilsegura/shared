<?php

declare(strict_types=1);

namespace Shared\Tests\Criteria\Expr;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shared\Criteria\Expr\AndX;
use Shared\Criteria\Expr\Comparison;
use Shared\Criteria\Expr\InvalidExpressionException;
use Shared\Criteria\Expr\Operator;
use Shared\Criteria\Expr\Order;
use Shared\Criteria\Expr\OrderX;
use Shared\Criteria\Expr\OrX;
use Shared\Criteria\Expr\Sort;

final class InvalidExpressionExceptionTest extends TestCase
{
    #[Test]
    public function and_x_rejects_a_sort_expression(): void
    {
        self::expectException(InvalidExpressionException::class);

        new AndX(new Sort('id', Order::ASC));
    }

    #[Test]
    public function or_x_rejects_a_sort_expression(): void
    {
        self::expectException(InvalidExpressionException::class);

        new OrX(new Sort('id', Order::ASC));
    }

    #[Test]
    public function order_x_rejects_a_non_sort_expression(): void
    {
        self::expectException(InvalidExpressionException::class);

        new OrderX(new Comparison('id', Operator::EQ, 1));
    }
}

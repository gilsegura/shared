<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

use Shared\Criteria\ExpressionInterface;

final readonly class Sort implements ExpressionInterface
{
    public function __construct(
        public string $field,
        public Order $order,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

use Shared\Criteria\ExpressionInterface;

final readonly class Comparison implements ExpressionInterface
{
    public function __construct(
        public string $field,
        public Operator $operator,
        public mixed $value,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

use Shared\Criteria\ExpressionInterface;

final readonly class OrderX extends Composite
{
    public function __construct(ExpressionInterface ...$expressions)
    {
        if (!array_all($expressions, static fn (ExpressionInterface $expression): bool => $expression instanceof Sort)) {
            throw new \InvalidArgumentException('OrderX can only contain Sort expressions.');
        }

        parent::__construct(...$expressions);
    }
}

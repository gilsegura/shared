<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

use Shared\Criteria\ExpressionInterface;

/**
 * Expression describing an ordering over a field.
 */
final readonly class OrderX extends Composite
{
    public function __construct(ExpressionInterface ...$expressions)
    {
        if (!array_all($expressions, static fn (ExpressionInterface $expression): bool => $expression instanceof Sort)) {
            throw InvalidExpressionException::onlySortAllowed('OrderX');
        }

        parent::__construct(...$expressions);
    }
}

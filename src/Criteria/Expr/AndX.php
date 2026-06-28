<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

use Shared\Criteria\ExpressionInterface;

/**
 * Expression node combining sub-expressions with AND.
 */
final readonly class AndX extends Composite
{
    public function __construct(ExpressionInterface ...$expressions)
    {
        if (!array_all($expressions, static fn (ExpressionInterface $expression): bool => !$expression instanceof Sort)) {
            throw InvalidExpressionException::sortNotAllowed('AndX');
        }

        parent::__construct(...$expressions);
    }
}

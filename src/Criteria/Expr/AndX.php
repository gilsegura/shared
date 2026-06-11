<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

use Shared\Criteria\ExpressionInterface;

final readonly class AndX extends Composite
{
    public function __construct(ExpressionInterface ...$expressions)
    {
        if (!array_all($expressions, static fn (ExpressionInterface $expression): bool => !$expression instanceof Sort)) {
            throw new \InvalidArgumentException('AndX cannot contain a Sort expression.');
        }

        parent::__construct(...$expressions);
    }
}

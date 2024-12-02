<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

use Shared\Criteria\ExpressionInterface;

abstract readonly class Composite implements ExpressionInterface
{
    /** @var ExpressionInterface[] */
    public array $expressions;

    public function __construct(
        ExpressionInterface ...$expressions,
    ) {
        $this->expressions = $expressions;
    }
}

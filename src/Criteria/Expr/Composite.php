<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

use Shared\Criteria\ExpressionInterface;

/**
 * Base for expression nodes that group other expressions.
 */
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

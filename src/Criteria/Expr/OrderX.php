<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

use ProxyAssert\Assertion;
use Shared\Criteria\ExpressionInterface;

final readonly class OrderX extends Composite
{
    public function __construct(ExpressionInterface ...$expressions)
    {
        Assertion::allIsInstanceOf($expressions, Sort::class);

        parent::__construct(...$expressions);
    }
}

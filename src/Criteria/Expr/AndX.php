<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

use ProxyAssert\Assertion;
use Shared\Criteria\ExpressionInterface;

final readonly class AndX extends Composite
{
    public function __construct(ExpressionInterface ...$expressions)
    {
        Assertion::allNotIsInstanceOf($expressions, Sort::class);

        parent::__construct(...$expressions);
    }
}

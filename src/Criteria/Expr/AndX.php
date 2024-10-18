<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

final readonly class AndX extends Composite
{
    public function __construct(AndX|OrX|Comparison ...$expressions)
    {
        parent::__construct(...$expressions);
    }
}

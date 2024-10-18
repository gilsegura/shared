<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

final readonly class OrX extends Composite
{
    public function __construct(AndX|OrX|Comparison ...$expressions)
    {
        parent::__construct(...$expressions);
    }
}

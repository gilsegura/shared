<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

final readonly class OrderX extends Composite
{
    public function __construct(Sort ...$expressions)
    {
        parent::__construct(...$expressions);
    }
}

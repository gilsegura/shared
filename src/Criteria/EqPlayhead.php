<?php

declare(strict_types=1);

namespace Shared\Criteria;

use Shared\Criteria\Expr\Comparison;
use Shared\Criteria\Expr\Operator;

final readonly class EqPlayhead extends AbstractCriteria
{
    public function __construct(
        int $playhead,
    ) {
        parent::__construct(new Comparison('playhead', Operator::EQ, $playhead));
    }
}

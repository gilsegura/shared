<?php

declare(strict_types=1);

namespace Shared\Criteria;

use Shared\Criteria\Expr\Comparison;
use Shared\Criteria\Expr\Operator;
use Shared\Domain\Uuid;

final readonly class EqId extends AbstractCriteria
{
    public function __construct(
        Uuid $id,
    ) {
        parent::__construct(new Comparison('id', Operator::EQ, $id));
    }
}

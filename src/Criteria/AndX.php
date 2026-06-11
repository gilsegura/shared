<?php

declare(strict_types=1);

namespace Shared\Criteria;

final readonly class AndX extends AbstractCriteria
{
    use MapsCriteriaToExpressionsTrait;

    public function __construct(
        CriteriaInterface ...$criteria,
    ) {
        parent::__construct(new Expr\AndX(...self::toExpressions($criteria)));
    }
}

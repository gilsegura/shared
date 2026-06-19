<?php

declare(strict_types=1);

namespace Shared\Criteria;

/**
 * Combines criteria that must all hold (logical AND). An empty AndX
 * matches everything.
 */
final readonly class AndX extends AbstractCriteria
{
    use MapsCriteriaToExpressionsTrait;

    public function __construct(
        CriteriaInterface ...$criteria,
    ) {
        parent::__construct(new Expr\AndX(...self::toExpressions($criteria)));
    }
}

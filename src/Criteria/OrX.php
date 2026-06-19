<?php

declare(strict_types=1);

namespace Shared\Criteria;

/**
 * Combines criteria where at least one must hold (logical OR).
 */
final readonly class OrX extends AbstractCriteria
{
    use MapsCriteriaToExpressionsTrait;

    public function __construct(
        CriteriaInterface ...$criteria,
    ) {
        parent::__construct(new Expr\OrX(...self::toExpressions($criteria)));
    }
}

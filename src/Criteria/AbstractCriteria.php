<?php

declare(strict_types=1);

namespace Shared\Criteria;

/**
 * Base for criteria that wrap an expression tree, exposing it for
 * translation.
 */
abstract readonly class AbstractCriteria implements CriteriaInterface
{
    public function __construct(
        private ExpressionInterface $expr,
    ) {
    }

    #[\Override]
    public function expr(): ExpressionInterface
    {
        return $this->expr;
    }
}

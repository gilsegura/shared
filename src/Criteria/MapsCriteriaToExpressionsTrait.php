<?php

declare(strict_types=1);

namespace Shared\Criteria;

trait MapsCriteriaToExpressionsTrait
{
    /**
     * @param CriteriaInterface[] $criteria
     *
     * @return ExpressionInterface[]
     */
    private static function toExpressions(array $criteria): array
    {
        return array_map(
            static fn (CriteriaInterface $criterion): ExpressionInterface => $criterion->expr(),
            $criteria,
        );
    }
}

<?php

declare(strict_types=1);

namespace Shared\Criteria;

/**
 * A criterion expressed in domain terms, independent of any storage; an
 * adapter maps it to a concrete query.
 */
interface CriteriaInterface
{
    public function expr(): ExpressionInterface;
}

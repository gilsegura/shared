<?php

declare(strict_types=1);

namespace Shared\Criteria;

interface CriteriaInterface
{
    public function expr(): ExpressionInterface;
}

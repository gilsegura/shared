<?php

declare(strict_types=1);

namespace Shared\Criteria;

use Shared\Criteria\Expr\Order;
use Shared\Criteria\Expr\Sort;

final readonly class ByRecordedAt extends AbstractCriteria
{
    public function __construct(
        Order $order,
    ) {
        parent::__construct(new Sort('recordedAt', $order));
    }
}

<?php

declare(strict_types=1);

namespace Shared\Criteria;

use Shared\Criteria\Expr\Order;
use Shared\Criteria\Expr\Sort;

/**
 * Orders results by playhead, the per-aggregate position of an event and
 * the natural order for replay.
 */
final readonly class ByPlayhead extends AbstractCriteria
{
    public function __construct(
        Order $order,
    ) {
        parent::__construct(new Sort('playhead', $order));
    }
}

<?php

declare(strict_types=1);

namespace Shared\Criteria;

use Shared\Criteria\Expr\Comparison;
use Shared\Criteria\Expr\Operator;

/**
 * Matches messages whose playhead is greater than or equal to the given value.
 * Used to resume a stream from a position, e.g. loading only the events recorded
 * after a snapshot.
 */
final readonly class GtePlayhead extends AbstractCriteria
{
    public function __construct(
        int $playhead,
    ) {
        parent::__construct(new Comparison('playhead', Operator::GTE, $playhead));
    }
}

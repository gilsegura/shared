<?php

declare(strict_types=1);

namespace Shared\Criteria;

use Shared\Criteria\Expr\Comparison;
use Shared\Criteria\Expr\Operator;
use Shared\Domain\Email;

/**
 * Matches records whose email equals the given value.
 */
final readonly class EqEmail extends AbstractCriteria
{
    public function __construct(
        Email $email,
    ) {
        parent::__construct(new Comparison('email', Operator::EQ, $email));
    }
}

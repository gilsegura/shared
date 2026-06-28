<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

use Shared\Exception\UnexpectedException;

/**
 * Raised when an expression composite is built with expressions it cannot hold,
 * e.g. a Sort placed inside an AndX/OrX, or a non-Sort inside an OrderX. This is
 * a programming error in how the criteria were composed.
 */
final class InvalidExpressionException extends UnexpectedException
{
    public static function sortNotAllowed(string $composite): self
    {
        return new self(\sprintf('%s cannot contain a Sort expression.', $composite));
    }

    public static function onlySortAllowed(string $composite): self
    {
        return new self(\sprintf('%s can only contain Sort expressions.', $composite));
    }
}

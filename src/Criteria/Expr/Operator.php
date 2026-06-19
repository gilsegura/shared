<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

/**
 * The comparison operators available to expressions.
 */
enum Operator: string
{
    case EQ = '=';
    case NEQ = '<>';
    case GT = '>';
    case GTE = '>=';
    case LT = '<';
    case LTE = '<=';
    case IN = 'IN';
    case NIN = 'NIN';
    case CONTAINS = 'CONTAINS';
}

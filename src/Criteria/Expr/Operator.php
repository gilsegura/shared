<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

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

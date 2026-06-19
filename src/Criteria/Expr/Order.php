<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

/**
 * Sort direction (ascending or descending).
 */
enum Order: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}

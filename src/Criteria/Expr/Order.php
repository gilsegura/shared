<?php

declare(strict_types=1);

namespace Shared\Criteria\Expr;

enum Order: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}

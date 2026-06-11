<?php

declare(strict_types=1);

namespace Shared\Query;

/**
 * Query that represents a single entity result (findOne).
 *
 * @template TEntity
 */
abstract readonly class SingleResultQuery extends QueryBuilder
{
}

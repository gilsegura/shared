<?php

declare(strict_types=1);

namespace Shared\Query;

use Shared\CommandHandling\QueryInterface;

/**
 * Query that represents a single entity result (findOne).
 *
 * @template TResult
 *
 * @implements QueryInterface<TResult|null>
 */
abstract readonly class SingleResultQuery extends QueryBuilder implements QueryInterface
{
}

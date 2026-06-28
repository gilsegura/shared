<?php

declare(strict_types=1);

namespace Shared\Query;

use Shared\CommandHandling\QueryInterface;
use Shared\Criteria\AndX;
use Shared\Criteria\OrderX;
use Shared\Criteria\OrX;

/**
 * Query that represents a collection result (findMany).
 *
 * @template TResult
 *
 * @implements QueryInterface<TResult[]>
 */
abstract class CollectionQuery extends QueryBuilder implements QueryInterface
{
    protected function __construct(
        AndX|OrX|null $criteria = null,
        protected ?OrderX $sort = null,
        protected ?int $offset = null,
        protected ?int $limit = null,
    ) {
        parent::__construct($criteria);
    }

    final public function orderBy(OrderX $sort): static
    {
        return $this->with('sort', $sort);
    }

    final public function withOffset(int $offset): static
    {
        return $this->with('offset', $offset);
    }

    final public function withLimit(int $limit): static
    {
        return $this->with('limit', $limit);
    }

    final public function order(): ?OrderX
    {
        return $this->sort;
    }

    final public function offset(): ?int
    {
        return $this->offset;
    }

    final public function limit(): ?int
    {
        return $this->limit;
    }
}

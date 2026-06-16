<?php

declare(strict_types=1);

namespace Shared\Query;

use Shared\CommandHandling\QueryInterface;
use Shared\Criteria\AndX;
use Shared\Criteria\CriteriaInterface;
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
        AndX|OrX|CriteriaInterface|null $criteria = null,
        protected ?OrderX $sort = null,
        protected ?int $offset = null,
        protected ?int $limit = null,
    ) {
        parent::__construct($criteria);
    }

    public function orderBy(OrderX $sort): static
    {
        return $this->with('sort', $sort);
    }

    public function withOffset(int $offset): static
    {
        return $this->with('offset', $offset);
    }

    public function withLimit(int $limit): static
    {
        return $this->with('limit', $limit);
    }

    public function order(): ?OrderX
    {
        return $this->sort;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }
}

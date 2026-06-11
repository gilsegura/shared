<?php

declare(strict_types=1);

namespace Shared\Query;

use Shared\Criteria\AndX;
use Shared\Criteria\CriteriaInterface;
use Shared\Criteria\OrderX;
use Shared\Criteria\OrX;

/**
 * Query that represents a collection result (findMany).
 *
 * @template TEntity
 */
abstract readonly class CollectionQuery extends QueryBuilder
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
        return clone ($this, [
            'sort' => $sort,
        ]);
    }

    public function withOffset(int $offset): static
    {
        return clone ($this, [
            'offset' => $offset,
        ]);
    }

    public function withLimit(int $limit): static
    {
        return clone ($this, [
            'limit' => $limit,
        ]);
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

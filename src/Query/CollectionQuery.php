<?php

declare(strict_types=1);

namespace Shared\Query;

use Shared\CommandHandling\QueryInterface;
use Shared\Criteria\AndX;
use Shared\Criteria\OrderX;
use Shared\Criteria\OrX;

/**
 * Query that represents a paginated collection result. It yields a Page<TResult>
 * — the items plus the total — so the result type carried to the bus is the page
 * itself, not a bare list, and every collection query shares one result shape
 * without a per-query page class.
 *
 * @template TResult
 *
 * @implements QueryInterface<Page<TResult>>
 */
abstract class CollectionQuery extends QueryBuilder implements QueryInterface
{
    protected function __construct(
        AndX|OrX|null $criteria = null,
        protected ?OrderX $sort = null,
        protected ?Pagination $pagination = null,
    ) {
        parent::__construct($criteria);
    }

    final public function orderBy(OrderX $sort): static
    {
        return $this->with('sort', $sort);
    }

    final public function paginate(Pagination $pagination): static
    {
        return $this->with('pagination', $pagination);
    }

    final public function order(): ?OrderX
    {
        return $this->sort;
    }

    /**
     * The requested pagination, or the conventional first page when the query was
     * run without paginating.
     */
    final public function pagination(): Pagination
    {
        return $this->pagination ?? Pagination::default();
    }
}

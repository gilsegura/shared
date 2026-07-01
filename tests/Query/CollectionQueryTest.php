<?php

declare(strict_types=1);

namespace Shared\Tests\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shared\Criteria\ByPlayhead;
use Shared\Criteria\EqId;
use Shared\Criteria\Expr\Order;
use Shared\Criteria\OrderX;
use Shared\Domain\Uuid;
use Shared\Query\CollectionQuery;
use Shared\Query\Pagination;

final class CollectionQueryTest extends TestCase
{
    #[Test]
    public function an_empty_collection_query_has_no_sort_and_defaults_its_pagination(): void
    {
        $query = TestCollectionQuery::of();

        self::assertNull($query->order());
        // Unpaginated queries fall back to the conventional first page.
        self::assertSame(0, $query->pagination()->offset);
        self::assertSame(20, $query->pagination()->limit);
    }

    #[Test]
    public function order_by_sets_the_sort(): void
    {
        $sort = new OrderX(new ByPlayhead(Order::ASC));

        $query = TestCollectionQuery::of()->orderBy($sort);

        self::assertSame($sort, $query->order());
    }

    #[Test]
    public function paginate_sets_the_pagination(): void
    {
        $query = TestCollectionQuery::of()->paginate(Pagination::of(10, 20));

        self::assertSame(10, $query->pagination()->offset);
        self::assertSame(20, $query->pagination()->limit);
    }

    #[Test]
    public function a_collection_query_is_immutable(): void
    {
        $base = TestCollectionQuery::of();
        $paginated = $base->paginate(Pagination::of(0, 5));

        self::assertSame(20, $base->pagination()->limit);
        self::assertSame(5, $paginated->pagination()->limit);
    }

    #[Test]
    public function it_keeps_criteria_while_paginating(): void
    {
        $query = TestCollectionQuery::of()
            ->withId(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'))
            ->paginate(Pagination::of(0, 5));

        self::assertNotNull($query->criteria());
        self::assertSame(5, $query->pagination()->limit);
    }
}

/**
 * @extends CollectionQuery<object>
 */
final class TestCollectionQuery extends CollectionQuery
{
    public static function of(): self
    {
        return new self();
    }

    public function withId(Uuid $id): self
    {
        return $this->where(new EqId($id));
    }
}

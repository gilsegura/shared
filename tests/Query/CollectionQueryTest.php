<?php

declare(strict_types=1);

namespace Shared\Tests\Query;

use PHPUnit\Framework\TestCase;
use Shared\Criteria\ByPlayhead;
use Shared\Criteria\EqId;
use Shared\Criteria\Expr\Order;
use Shared\Criteria\OrderX;
use Shared\Domain\Uuid;
use Shared\Query\CollectionQuery;

final class CollectionQueryTest extends TestCase
{
    public function test_empty_collection_query_has_no_sort_offset_or_limit(): void
    {
        $query = TestCollectionQuery::of();

        self::assertNull($query->order());
        self::assertNull($query->offset());
        self::assertNull($query->limit());
    }

    public function test_order_by_sets_the_sort(): void
    {
        $sort = new OrderX(new ByPlayhead(Order::ASC));

        $query = TestCollectionQuery::of()->orderBy($sort);

        self::assertSame($sort, $query->order());
    }

    public function test_with_offset_and_limit_set_pagination(): void
    {
        $query = TestCollectionQuery::of()
            ->withOffset(10)
            ->withLimit(20);

        self::assertSame(10, $query->offset());
        self::assertSame(20, $query->limit());
    }

    public function test_collection_query_is_immutable(): void
    {
        $base = TestCollectionQuery::of();
        $paginated = $base->withLimit(5);

        self::assertNull($base->limit());
        self::assertSame(5, $paginated->limit());
    }

    public function test_keeps_criteria_while_paginating(): void
    {
        $query = TestCollectionQuery::of()
            ->withId(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'))
            ->withLimit(5);

        self::assertNotNull($query->criteria());
        self::assertSame(5, $query->limit());
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

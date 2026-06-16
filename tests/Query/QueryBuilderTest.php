<?php

declare(strict_types=1);

namespace Shared\Tests\Query;

use PHPUnit\Framework\TestCase;
use Shared\Criteria\AndX;
use Shared\Criteria\CriteriaInterface;
use Shared\Criteria\EqId;
use Shared\Criteria\EqPlayhead;
use Shared\Criteria\OrX;
use Shared\Domain\Uuid;
use Shared\Query\SingleResultQuery;

final class QueryBuilderTest extends TestCase
{
    public function test_empty_query_has_no_criteria(): void
    {
        $query = TestQuery::of();

        self::assertNull($query->criteria());
    }

    public function test_single_where_sets_a_single_criterion(): void
    {
        $query = TestQuery::of()->withId(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'));

        self::assertInstanceOf(CriteriaInterface::class, $query->criteria());
        self::assertNotInstanceOf(AndX::class, $query->criteria());
    }

    public function test_chained_where_combines_with_and(): void
    {
        $query = TestQuery::of()
            ->withId(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'))
            ->withPlayhead(1);

        self::assertInstanceOf(AndX::class, $query->criteria());
    }

    public function test_or_x_combines_criteria_with_or(): void
    {
        $query = TestQuery::of()
            ->withId(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'))
            ->orWhere(static fn (TestQuery $sub): TestQuery => $sub->withPlayhead(1));

        self::assertInstanceOf(OrX::class, $query->criteria());
    }

    public function test_or_x_without_base_criteria_does_not_wrap_in_null(): void
    {
        $query = TestQuery::of()
            ->orWhere(static fn (TestQuery $sub): TestQuery => $sub->withPlayhead(1));

        self::assertInstanceOf(OrX::class, $query->criteria());
    }

    public function test_query_is_immutable(): void
    {
        $base = TestQuery::of();
        $withId = $base->withId(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'));

        self::assertNull($base->criteria());
        self::assertNotNull($withId->criteria());
    }
}

/**
 * @extends SingleResultQuery<object>
 */
final class TestQuery extends SingleResultQuery
{
    public static function of(): self
    {
        return new self();
    }

    public function withId(Uuid $id): self
    {
        return $this->where(new EqId($id));
    }

    public function withPlayhead(int $playhead): self
    {
        return $this->where(new EqPlayhead($playhead));
    }

    /**
     * @param callable(self): self $callback
     */
    public function orWhere(callable $callback): self
    {
        return $this->orX($callback);
    }
}

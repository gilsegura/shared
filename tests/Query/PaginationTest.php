<?php

declare(strict_types=1);

namespace Shared\Tests\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shared\Query\Page;
use Shared\Query\Pagination;

final class PaginationTest extends TestCase
{
    #[Test]
    public function it_holds_offset_and_limit(): void
    {
        $pagination = Pagination::of(10, 20);

        self::assertSame(10, $pagination->offset);
        self::assertSame(20, $pagination->limit);
    }

    #[Test]
    public function it_defaults_to_the_first_page(): void
    {
        $pagination = Pagination::default();

        self::assertSame(0, $pagination->offset);
        self::assertSame(20, $pagination->limit);
    }

    #[Test]
    public function it_rejects_a_negative_offset(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Pagination::of(-1, 20);
    }

    #[Test]
    public function it_rejects_a_limit_below_one(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Pagination::of(0, 0);
    }

    #[Test]
    public function a_page_carries_its_items_total_and_pagination(): void
    {
        $pagination = Pagination::of(0, 2);

        $page = new Page(['a', 'b'], 5, $pagination);

        self::assertSame(['a', 'b'], $page->items);
        self::assertSame(5, $page->total);
        self::assertSame($pagination, $page->pagination);
    }
}

<?php

declare(strict_types=1);

namespace Shared\Query;

/**
 * A page of results: the items, the total across the whole set, and the
 * pagination that produced them. Generic, so every collection query returns its
 * own typed page; the read side builds it, and a delivery layer renders links or
 * meta from its total. Self-contained — it carries the Pagination it came from,
 * so a caller need not reconcile it with the original request.
 *
 * @template-covariant T
 */
final readonly class Page
{
    /**
     * @param T[] $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public Pagination $pagination,
    ) {
    }
}

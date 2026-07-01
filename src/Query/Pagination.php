<?php

declare(strict_types=1);

namespace Shared\Query;

/**
 * A request for a slice of a result set: an offset and a limit, validated
 * together. Carried by a collection query and used by its handler to page the
 * read side, instead of passing two loose ints through every layer.
 */
final readonly class Pagination
{
    private function __construct(
        public int $offset,
        public int $limit,
    ) {
    }

    public static function of(int $offset, int $limit): self
    {
        if ($offset < 0) {
            throw new \InvalidArgumentException('Pagination offset must not be negative.');
        }

        if ($limit < 1) {
            throw new \InvalidArgumentException('Pagination limit must be at least 1.');
        }

        return new self($offset, $limit);
    }

    /**
     * The conventional first page, used when a query is run without paginating.
     */
    public static function default(int $limit = 20): self
    {
        return new self(0, $limit);
    }
}

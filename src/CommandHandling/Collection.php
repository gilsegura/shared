<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

final readonly class Collection
{
    /**
     * @param Item[] $data
     *
     * @throws PageNotFoundException
     */
    public function __construct(
        public int $page,
        public int $limit,
        public int $total,
        public array $data,
    ) {
        $this->exists($page, $limit, $total);
    }

    /**
     * @throws PageNotFoundException
     */
    private function exists(int $page, int $limit, int $total): void
    {
        if (0 === $total) {
            return;
        }

        if ($limit * ($page - 1) < $total) {
            return;
        }

        throw PageNotFoundException::new($page);
    }
}

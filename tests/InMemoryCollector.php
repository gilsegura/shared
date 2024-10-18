<?php

declare(strict_types=1);

namespace Shared\Tests;

final class InMemoryCollector
{
    /** @var object[] */
    private array $objects = [];

    public function collect(object $object): void
    {
        $this->objects[] = $object;
    }

    /**
     * @return object[]
     */
    public function objects(): array
    {
        return $this->objects;
    }
}

<?php

declare(strict_types=1);

namespace Shared\Specification;

abstract readonly class AbstractSpecification
{
    abstract protected function isSatisfiedBy(mixed ...$params): bool;
}

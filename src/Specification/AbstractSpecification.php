<?php

declare(strict_types=1);

namespace Shared\Specification;

/**
 * Base for composable domain specifications (business rules expressed as
 * objects).
 */
abstract readonly class AbstractSpecification
{
    abstract protected function isSatisfiedBy(mixed ...$params): bool;
}

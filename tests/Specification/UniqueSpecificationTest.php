<?php

declare(strict_types=1);

namespace Shared\Tests\Specification;

use PHPUnit\Framework\TestCase;
use Shared\Specification\AbstractSpecification;

final class UniqueSpecificationTest extends TestCase
{
    public function test_must_throw_exception_when_guard_is_not_unique(): void
    {
        self::expectException(\Exception::class);

        $specification = new ThrowableSpecification();

        $specification->isUnique();
    }

    public function test_must_guard_is_unique(): void
    {
        $specification = new Specification();

        $isUnique = $specification->isUnique();

        self::assertTrue($isUnique);
    }
}

final readonly class Specification extends AbstractSpecification
{
    public function isUnique(): bool
    {
        return $this->isSatisfiedBy();
    }

    #[\Override]
    protected function isSatisfiedBy(mixed ...$params): bool
    {
        return true;
    }
}

final readonly class ThrowableSpecification extends AbstractSpecification
{
    /**
     * @throws \Exception
     */
    public function isUnique(): bool
    {
        return $this->isSatisfiedBy();
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    protected function isSatisfiedBy(mixed ...$params): bool
    {
        throw new \Exception();
    }
}

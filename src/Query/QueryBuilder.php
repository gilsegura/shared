<?php

declare(strict_types=1);

namespace Shared\Query;

use Shared\Criteria\AndX;
use Shared\Criteria\CriteriaInterface;
use Shared\Criteria\OrX;

/**
 * Base DSL builder. Effectively immutable: it exposes no setters, only fluent
 * operations that return a clone with one property changed. The properties are
 * not readonly because a builder is reconstructed by cloning (PHP cannot rewrite
 * a readonly property on a clone), but nothing mutates an existing instance.
 */
abstract class QueryBuilder
{
    protected function __construct(
        protected AndX|OrX|CriteriaInterface|null $criteria = null,
    ) {
    }

    /**
     * Returns a clone of this builder with one property changed, preserving the
     * concrete subclass type.
     */
    protected function with(string $property, mixed $value): static
    {
        $clone = clone $this;
        $clone->{$property} = $value; // @phpstan-ignore property.dynamicName

        return $clone;
    }

    protected function where(CriteriaInterface $criteria): static
    {
        return $this->with('criteria', $this->criteria instanceof CriteriaInterface
            ? new AndX($this->criteria, $criteria)
            : $criteria);
    }

    /**
     * @param callable(static): static $callback
     */
    public function andX(callable $callback): static
    {
        return $this->with('criteria', new AndX(...$this->combine($callback)));
    }

    /**
     * @param callable(static): static $callback
     */
    public function orX(callable $callback): static
    {
        return $this->with('criteria', new OrX(...$this->combine($callback)));
    }

    public function criteria(): AndX|OrX|CriteriaInterface|null
    {
        return $this->criteria;
    }

    /**
     * Runs the sub-builder callback and returns this builder's criteria plus the
     * sub-builder's, dropping any that are absent.
     *
     * @param callable(static): static $callback
     *
     * @return CriteriaInterface[]
     */
    private function combine(callable $callback): array
    {
        $sub = $callback(clone $this);

        return array_values(array_filter(
            [$this->criteria, $sub->criteria()],
            static fn (AndX|OrX|CriteriaInterface|null $criterion): bool => $criterion instanceof CriteriaInterface,
        ));
    }
}

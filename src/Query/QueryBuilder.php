<?php

declare(strict_types=1);

namespace Shared\Query;

use Shared\Criteria\AndX;
use Shared\Criteria\CriteriaInterface;
use Shared\Criteria\OrX;

/**
 * Base DSL builder.
 */
abstract readonly class QueryBuilder
{
    protected function __construct(
        protected AndX|OrX|CriteriaInterface|null $criteria = null,
    ) {
    }

    protected function where(CriteriaInterface $criteria): static
    {
        return clone ($this, [
            'criteria' => $this->criteria instanceof CriteriaInterface
                ? new AndX($this->criteria, $criteria)
                : $criteria,
        ]);
    }

    private function sub(): static
    {
        return clone $this;
    }

    /**
     * @param callable(static): static $callback
     */
    public function andX(callable $callback): static
    {
        $sub = $callback($this->sub());

        $criteria = array_values(array_filter(
            [$this->criteria, $sub->criteria()],
            static fn (AndX|OrX|CriteriaInterface|null $criterion): bool => $criterion instanceof CriteriaInterface,
        ));

        return clone ($this, [
            'criteria' => new AndX(...$criteria),
        ]);
    }

    /**
     * @param callable(static): static $callback
     */
    public function orX(callable $callback): static
    {
        $sub = $callback($this->sub());

        $criteria = array_values(array_filter(
            [$this->criteria, $sub->criteria()],
            static fn (AndX|OrX|CriteriaInterface|null $criterion): bool => $criterion instanceof CriteriaInterface,
        ));

        return clone ($this, [
            'criteria' => new OrX(...$criteria),
        ]);
    }

    public function criteria(): AndX|OrX|CriteriaInterface|null
    {
        return $this->criteria;
    }
}

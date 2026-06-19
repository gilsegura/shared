<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

/**
 * Dispatches a query to its handler and returns its result. An adapter
 * provides the concrete bus.
 */
interface QueryBusInterface
{
    /**
     * @template TResult
     *
     * @param QueryInterface<TResult> $query
     *
     * @return TResult
     */
    public function __invoke(QueryInterface $query): mixed;
}

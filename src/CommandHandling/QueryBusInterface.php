<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

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

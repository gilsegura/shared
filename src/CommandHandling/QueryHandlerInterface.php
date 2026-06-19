<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

/**
 * Handles a single query type and returns its result. Routed to the query
 * bus by the integration layer.
 *
 * @template TResult
 * @template TQuery of QueryInterface<TResult>
 */
interface QueryHandlerInterface
{
}

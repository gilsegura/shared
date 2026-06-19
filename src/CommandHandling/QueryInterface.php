<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

/**
 * Marker for a query: a request for data, carrying the type of result it
 * yields.
 *
 * @template-covariant TResult
 */
interface QueryInterface
{
}

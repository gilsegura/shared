<?php

declare(strict_types=1);

namespace Shared\Index;

use Shared\Exception\InfrastructureException;

/**
 * Raised when an index operation (lookup, save or remove) fails.
 */
final class IndexException extends InfrastructureException
{
    public static function fromThrowable(\Throwable $e): self
    {
        return new self($e->getMessage(), previous: $e);
    }
}

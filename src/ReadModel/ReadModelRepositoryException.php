<?php

declare(strict_types=1);

namespace Shared\ReadModel;

use Shared\Exception\InfrastructureException;

/**
 * Raised when a read model repository operation fails.
 */
final class ReadModelRepositoryException extends InfrastructureException
{
    public static function throwable(\Throwable $e): self
    {
        return new self($e->getMessage(), previous: $e);
    }
}

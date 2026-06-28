<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Exception\ConflictException;

/**
 * Raised when creating an aggregate whose stream already exists.
 */
final class AggregateRootAlreadyExistsException extends ConflictException
{
    public static function fromClassName(string $className): self
    {
        return new self(sprintf('The requested aggregate root "%s" already exists.', $className));
    }
}

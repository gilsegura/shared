<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Exception\ConflictException;

final class AggregateRootAlreadyExistsException extends ConflictException
{
    public static function className(string $className): self
    {
        return new self(sprintf('The requested aggregate root "%s" already exists.', $className));
    }
}

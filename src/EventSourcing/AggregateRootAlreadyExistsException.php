<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Exception\AlreadyExistsException;

final class AggregateRootAlreadyExistsException extends AlreadyExistsException
{
    public static function new(AggregateRootInterface $aggregateRoot): self
    {
        return new self(sprintf('AggregateRoot "%s" already exists.', $aggregateRoot::class));
    }
}

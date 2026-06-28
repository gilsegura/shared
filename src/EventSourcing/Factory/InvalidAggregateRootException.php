<?php

declare(strict_types=1);

namespace Shared\EventSourcing\Factory;

use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;
use Shared\Exception\UnexpectedException;

/**
 * Raised when a factory is asked to build a class that is not an event-sourced
 * aggregate, i.e. it does not extend AbstractEventSourcedAggregateRoot. This is a
 * configuration error: the wrong class string was handed to the factory.
 */
final class InvalidAggregateRootException extends UnexpectedException
{
    /**
     * @param class-string $className
     */
    public static function mustExtendAggregateRoot(string $className): self
    {
        return new self(\sprintf('Class "%s" must extend %s.', $className, AbstractEventSourcedAggregateRoot::class));
    }
}

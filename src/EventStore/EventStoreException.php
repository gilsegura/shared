<?php

declare(strict_types=1);

namespace Shared\EventStore;

final class EventStoreException extends \RuntimeException
{
    public static function new(?\Throwable $throwable = null): self
    {
        return new self($throwable?->getMessage(), 0, $throwable);
    }
}

<?php

declare(strict_types=1);

namespace Shared\EventHandling;

final class EventBusException extends \RuntimeException
{
    public static function new(?\Throwable $e = null): self
    {
        return new self($e?->getMessage(), 0, $e);
    }
}

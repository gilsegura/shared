<?php

declare(strict_types=1);

namespace Shared\EventHandling;

final class EventBusException extends \Exception
{
    public static function throwable(\Throwable $e): self
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}

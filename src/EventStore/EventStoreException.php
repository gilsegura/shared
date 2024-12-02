<?php

declare(strict_types=1);

namespace Shared\EventStore;

final class EventStoreException extends \Exception
{
    public static function throwable(\Throwable $e): self
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}

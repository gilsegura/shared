<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

final class EventSourcingRepositoryException extends \Exception
{
    public static function throwable(\Throwable $e): self
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}

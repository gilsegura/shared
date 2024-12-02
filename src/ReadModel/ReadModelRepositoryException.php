<?php

declare(strict_types=1);

namespace Shared\ReadModel;

final class ReadModelRepositoryException extends \Exception
{
    public static function throwable(\Throwable $e): self
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}

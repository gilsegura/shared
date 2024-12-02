<?php

declare(strict_types=1);

namespace Shared\Domain;

class DomainException extends \Exception
{
    public function __construct(
        string $message = 'Conflict.',
        int $code = 409,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function throwable(\Throwable $e): self
    {
        return new self($e->getMessage(), 409, $e);
    }
}
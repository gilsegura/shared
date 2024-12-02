<?php

declare(strict_types=1);

namespace Shared\Exception;

class NotFoundException extends \Exception
{
    public function __construct(
        string $message = 'Not found.',
        int $code = 404,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function throwable(\Throwable $e): self
    {
        return new self($e->getMessage(), 404, $e);
    }
}

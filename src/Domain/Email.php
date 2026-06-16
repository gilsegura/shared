<?php

declare(strict_types=1);

namespace Shared\Domain;

use Shared\Exception\InvalidInputException;

final readonly class Email
{
    public function __construct(
        public string $email,
    ) {
        if (false === filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw InvalidInputException::notAValidEmail($this->email);
        }
    }

    public function equals(Email $email): bool
    {
        return 0 === strcasecmp($this->email, $email->email);
    }
}

<?php

declare(strict_types=1);

namespace Shared\Domain;

final readonly class Email
{
    public function __construct(
        public string $email,
    ) {
        if (false === filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Value must be a valid email address.');
        }
    }

    public function equals(Email $email): bool
    {
        return $this->email === $email->email;
    }
}

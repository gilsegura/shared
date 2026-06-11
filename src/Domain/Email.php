<?php

declare(strict_types=1);

namespace Shared\Domain;

final readonly class Email
{
    public string $email;

    public function __construct(
        string $email,
    ) {
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Value must be a valid email address.');
        }

        $this->email = $email;
    }

    public function equals(Email $email): bool
    {
        return $this->email === $email->email;
    }
}

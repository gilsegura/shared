<?php

declare(strict_types=1);

namespace Shared\Domain;

/**
 * An email value object with validation and equality.
 */
final readonly class Email
{
    public function __construct(
        public string $email,
    ) {
        if (false === filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(\sprintf('The value "%s" is not a valid email address.', $this->email));
        }
    }

    public function equals(Email $email): bool
    {
        return 0 === strcasecmp($this->email, $email->email);
    }
}

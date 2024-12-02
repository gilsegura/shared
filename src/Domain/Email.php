<?php

declare(strict_types=1);

namespace Shared\Domain;

use ProxyAssert\Assertion;

final readonly class Email
{
    public string $email;

    public function __construct(
        string $email,
    ) {
        Assertion::email($email);

        $this->email = $email;
    }

    public function equals(Email $email): bool
    {
        return $this->email === $email->email;
    }
}

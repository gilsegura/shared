<?php

declare(strict_types=1);

namespace Shared\Domain;

use ProxyAssert\Assertion;

final readonly class HashedPassword
{
    private const int COST = 12;

    public function __construct(
        public string $password,
    ) {
    }

    public function equals(HashedPassword $password): bool
    {
        return $this->password === $password->password;
    }

    public static function encode(string $plainPassword): self
    {
        Assertion::minLength($plainPassword, 6);

        /** @var false|string $hashedPassword */
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => self::COST]);

        if (false === $hashedPassword) {
            throw new \RuntimeException('An error occurred while hashing password.');
        }

        return new self($hashedPassword);
    }

    public function match(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }
}

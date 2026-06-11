<?php

declare(strict_types=1);

namespace Shared\Domain;

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
        if (mb_strlen($plainPassword) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters long.');
        }

        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => self::COST]);

        return new self($hashedPassword);
    }

    public function match(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }
}

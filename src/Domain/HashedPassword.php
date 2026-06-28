<?php

declare(strict_types=1);

namespace Shared\Domain;

/**
 * A hashed password value object.
 */
final readonly class HashedPassword
{
    private const int COST = 12;

    private const int MINIMUM_LENGTH = 6;

    public function __construct(
        public string $password,
    ) {
    }

    public static function encode(string $plainPassword): self
    {
        if (mb_strlen($plainPassword) < self::MINIMUM_LENGTH) {
            throw new \InvalidArgumentException(\sprintf('The password must be at least %d characters long.', self::MINIMUM_LENGTH));
        }

        return new self(password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => self::COST]));
    }

    public function match(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }

    public function equals(HashedPassword $password): bool
    {
        return $this->password === $password->password;
    }
}

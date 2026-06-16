<?php

declare(strict_types=1);

namespace Shared\Exception;

/**
 * Thrown when a value object is built from input that violates its invariant:
 * a malformed UUID, an invalid email, an empty string, a too-short password.
 * The message names the problem and includes the offending value to aid
 * debugging.
 */
class InvalidInputException extends AbstractException
{
    public static function notAValidUuid(string $value): self
    {
        return new self(\sprintf('The value "%s" is not a valid UUID.', $value));
    }

    public static function notAValidEmail(string $value): self
    {
        return new self(\sprintf('The value "%s" is not a valid email address.', $value));
    }

    public static function emptyString(): self
    {
        return new self('The value must not be an empty string.');
    }

    public static function passwordTooShort(int $minimum): self
    {
        return new self(\sprintf('The password must be at least %d characters long.', $minimum));
    }
}

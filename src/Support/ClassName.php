<?php

declare(strict_types=1);

namespace Shared\Support;

/**
 * Helpers to derive text from class names.
 */
final readonly class ClassName
{
    /**
     * The short class name (without namespace) of a fully-qualified class name.
     */
    public static function short(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);

        return end($parts);
    }
}

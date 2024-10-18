<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

use Shared\Exception\NotFoundException;

final class PageNotFoundException extends NotFoundException
{
    public static function new(int $page): self
    {
        return new self(sprintf('Page "%s" not found.', $page));
    }
}

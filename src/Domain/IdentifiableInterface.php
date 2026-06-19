<?php

declare(strict_types=1);

namespace Shared\Domain;

/**
 * Something identified by a Uuid.
 */
interface IdentifiableInterface
{
    public function id(): Uuid;
}

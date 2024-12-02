<?php

declare(strict_types=1);

namespace Shared\Domain;

interface IdentifiableInterface
{
    public function id(): Uuid;
}

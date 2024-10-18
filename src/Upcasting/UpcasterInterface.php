<?php

declare(strict_types=1);

namespace Shared\Upcasting;

use Shared\Domain\DomainMessage;

interface UpcasterInterface
{
    public function supports(DomainMessage $message): bool;

    public function upcast(DomainMessage $message): DomainMessage;
}

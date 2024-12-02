<?php

declare(strict_types=1);

namespace Shared\Upcasting;

use Shared\Domain\DomainMessage;

interface UpcasterInterface
{
    public function __invoke(DomainMessage $message): DomainMessage;
}

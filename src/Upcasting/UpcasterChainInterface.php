<?php

declare(strict_types=1);

namespace Shared\Upcasting;

use Shared\Domain\DomainMessage;

interface UpcasterChainInterface
{
    public function upcast(DomainMessage $message): DomainMessage;
}

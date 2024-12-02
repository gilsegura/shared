<?php

declare(strict_types=1);

namespace Shared\Upcasting;

use Shared\Domain\DomainMessage;

interface UpcasterChainInterface
{
    /**
     * @return \Generator<DomainMessage>
     */
    public function __invoke(DomainMessage $message): \Generator;
}

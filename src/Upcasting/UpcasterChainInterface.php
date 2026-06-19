<?php

declare(strict_types=1);

namespace Shared\Upcasting;

use Shared\Domain\DomainMessage;

/**
 * Runs a domain message through a series of upcasters. Implementations yield the
 * resulting message(s); the generator return type lets a chain expand or filter
 * a message, though the sequential chain always yields exactly one.
 */
interface UpcasterChainInterface
{
    /**
     * @return \Generator<DomainMessage>
     */
    public function __invoke(DomainMessage $message): \Generator;
}

<?php

declare(strict_types=1);

namespace Shared\Upcasting;

use Shared\Domain\DomainMessage;

final readonly class SequentialUpcasterChain implements UpcasterChainInterface
{
    /** @var UpcasterInterface[] */
    private array $upcasters;

    public function __construct(
        UpcasterInterface ...$upcasters,
    ) {
        $this->upcasters = $upcasters;
    }

    #[\Override]
    public function __invoke(DomainMessage $message): \Generator
    {
        foreach ($this->upcasters as $upcaster) {
            yield $upcaster->__invoke($message);
        }
    }
}

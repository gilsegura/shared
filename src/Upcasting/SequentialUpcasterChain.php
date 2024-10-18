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
    public function upcast(DomainMessage $message): DomainMessage
    {
        foreach ($this->upcasters as $upcaster) {
            if ($upcaster->supports($message)) {
                $message = $upcaster->upcast($message);
            }
        }

        return $message;
    }
}

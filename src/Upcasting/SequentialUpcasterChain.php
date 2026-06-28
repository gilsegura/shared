<?php

declare(strict_types=1);

namespace Shared\Upcasting;

use Shared\Domain\DomainMessage;

/**
 * An upcaster chain that applies its upcasters in sequence: each one receives
 * the output of the previous, so a message is upgraded step by step (V1 to V2 to
 * V3). With no upcasters the message passes through untouched. It always yields
 * exactly one message.
 */
final readonly class SequentialUpcasterChain implements UpcasterChainInterface
{
    /** @var UpcasterInterface[] */
    private array $upcasters;

    public function __construct(
        UpcasterInterface ...$upcasters,
    ) {
        $this->upcasters = $upcasters;
    }

    /**
     * Runs the message through every upcaster in order: each upcaster receives
     * the output of the previous one. With no upcasters the message passes
     * through unchanged. Always yields exactly one message.
     */
    #[\Override]
    public function __invoke(DomainMessage $message): iterable
    {
        foreach ($this->upcasters as $upcaster) {
            $message = $upcaster($message);
        }

        yield $message;
    }
}

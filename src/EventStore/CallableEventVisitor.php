<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\DomainMessage;

/**
 * An event visitor that delegates each visited message to a closure.
 */
final readonly class CallableEventVisitor implements EventVisitorInterface
{
    /**
     * @param \Closure(DomainMessage): void $visitor
     */
    public function __construct(
        private \Closure $visitor,
    ) {
    }

    #[\Override]
    public function __invoke(DomainMessage $message): void
    {
        ($this->visitor)($message);
    }
}

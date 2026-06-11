<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\DomainMessage;

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

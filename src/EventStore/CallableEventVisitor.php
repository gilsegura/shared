<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\DomainMessage;

final readonly class CallableEventVisitor implements EventVisitorInterface
{
    public function __construct(
        private \Closure $callable,
    ) {
    }

    #[\Override]
    public function __invoke(DomainMessage $message): void
    {
        call_user_func($this->callable, $message);
    }
}

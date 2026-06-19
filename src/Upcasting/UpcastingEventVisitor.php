<?php

declare(strict_types=1);

namespace Shared\Upcasting;

use Shared\Domain\DomainMessage;
use Shared\EventStore\EventVisitorInterface;

/**
 * Decorates an event visitor so each visited message is upcast before reaching
 * it. The counterpart to UpcastingEventStore for the visiting path: the store
 * decorates the store, this decorates the visitor.
 */
final readonly class UpcastingEventVisitor implements EventVisitorInterface
{
    public function __construct(
        private EventVisitorInterface $eventVisitor,
        private SequentialUpcasterChain $upcaster,
    ) {
    }

    #[\Override]
    public function __invoke(DomainMessage $message): void
    {
        foreach (($this->upcaster)($message) as $upcasted) {
            ($this->eventVisitor)($upcasted);
        }
    }
}

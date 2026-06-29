<?php

declare(strict_types=1);

namespace Shared\EventSourcing\Register;

use Shared\EventHandling\EventBusInterface;
use Shared\EventSourcing\AggregateRootInterface;
use Shared\EventSourcing\AggregateRootRegisterInterface;
use Shared\EventSourcing\EventStreamDecoratorInterface;
use Shared\EventStore\EventStoreInterface;

/**
 * The base register: decorates the aggregate's uncommitted events, appends them
 * to the event store and publishes them on the event bus. This is the plain
 * write path — no snapshot — and the inner register that snapshotting wraps.
 *
 * @template-contravariant TAggregate of AggregateRootInterface
 *
 * @implements AggregateRootRegisterInterface<TAggregate>
 */
final readonly class EventStoreAggregateRootRegister implements AggregateRootRegisterInterface
{
    public function __construct(
        private EventStoreInterface $eventStore,
        private EventBusInterface $eventBus,
        private EventStreamDecoratorInterface $streamDecorator,
    ) {
    }

    /**
     * @param TAggregate $aggregateRoot
     */
    #[\Override]
    public function __invoke(AggregateRootInterface $aggregateRoot): void
    {
        $stream = ($this->streamDecorator)($aggregateRoot->uncommittedEvents());

        $this->eventStore->append($stream);
        ($this->eventBus)($stream);
    }
}

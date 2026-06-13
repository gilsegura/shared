<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\Uuid;
use Shared\EventHandling\EventBusInterface;
use Shared\EventStore\EventStoreInterface;

/**
 * @template TAggregate of AggregateRootInterface
 */
abstract readonly class AbstractEventSourcingRepository
{
    public function __construct(
        private EventStoreInterface $eventStore,
        private EventBusInterface $eventBus,
        private EventStreamDecoratorInterface $streamDecorator,
        private AggregateRootFactoryInterface $aggregateRootFactory,
    ) {
    }

    /**
     * @return TAggregate
     *
     * @throws EventSourcingRepositoryException
     */
    final protected function load(Uuid $id, ?int $playhead = null): AggregateRootInterface
    {
        try {
            $stream = $this->eventStore->load($id, $playhead);

            /** @var TAggregate $aggregateRoot */
            $aggregateRoot = ($this->aggregateRootFactory)($stream);

            return $aggregateRoot;
        } catch (\Throwable $e) {
            throw EventSourcingRepositoryException::throwable($e);
        }
    }

    /**
     * @param TAggregate $aggregateRoot
     *
     * @throws EventSourcingRepositoryException
     */
    final protected function save(AggregateRootInterface $aggregateRoot): void
    {
        try {
            $stream = ($this->streamDecorator)($aggregateRoot->uncommittedEvents());

            $this->eventStore->append($stream);
            ($this->eventBus)($stream);
        } catch (\Throwable $e) {
            throw EventSourcingRepositoryException::throwable($e);
        }
    }
}

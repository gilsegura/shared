<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\Uuid;
use Shared\EventHandling\EventBusInterface;
use Shared\EventStore\EventStoreInterface;

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
     * @throws EventSourcingRepositoryException
     */
    final protected function load(Uuid $id, ?int $playhead = null): AggregateRootInterface
    {
        try {
            $stream = $this->eventStore->load($id, $playhead);

            return $this->aggregateRootFactory->__invoke($stream);
        } catch (\Throwable $e) {
            throw EventSourcingRepositoryException::throwable($e);
        }
    }

    /**
     * @throws EventSourcingRepositoryException
     */
    final protected function save(AggregateRootInterface $aggregateRoot): void
    {
        try {
            $stream = $this->streamDecorator->__invoke($aggregateRoot->uncommittedEvents());

            $this->eventStore->append($stream);
            $this->eventBus->__invoke($stream);
        } catch (\Throwable $e) {
            throw EventSourcingRepositoryException::throwable($e);
        }
    }
}

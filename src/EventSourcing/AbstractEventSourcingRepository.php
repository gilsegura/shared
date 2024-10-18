<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\Uuid;
use Shared\EventHandling\EventBusException;
use Shared\EventHandling\EventBusInterface;
use Shared\EventStore\DomainEventStreamNotFoundException;
use Shared\EventStore\EventStoreException;
use Shared\EventStore\EventStoreInterface;
use Shared\EventStore\PlayheadAlreadyExistsException;

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
     * @throws DomainEventStreamNotFoundException
     */
    final protected function load(Uuid $id, ?int $playhead = null): AggregateRootInterface
    {
        $stream = $this->eventStore->load($id, $playhead);

        return $this->aggregateRootFactory->create($stream);
    }

    /**
     * @throws PlayheadAlreadyExistsException
     * @throws EventStoreException
     * @throws EventBusException
     */
    final protected function save(AggregateRootInterface $aggregateRoot): void
    {
        $stream = $this->streamDecorator->decorate($aggregateRoot->uncommittedEvents());

        $this->eventStore->append($stream);
        $this->eventBus->publish($stream);
    }
}

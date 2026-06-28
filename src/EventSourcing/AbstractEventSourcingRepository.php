<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\Uuid;
use Shared\EventHandling\EventBusInterface;
use Shared\EventStore\EventStoreInterface;
use Shared\Snapshotting\Snapshotter;

/**
 * @template TAggregate of AggregateRootInterface
 */
abstract readonly class AbstractEventSourcingRepository
{
    /**
     * Loading is delegated to a loader (the plain event-store loader, optionally
     * decorated by snapshotting), so the repository neither reads the stream nor
     * knows about snapshots on the read path. Writing appends, publishes, and —
     * when a snapshotter is given — captures a snapshot; with none (the default)
     * nothing is captured and behaviour is unchanged.
     *
     * @param AggregateRootLoaderInterface<TAggregate> $loader
     * @param Snapshotter<TAggregate>|null             $snapshotter
     */
    public function __construct(
        private AggregateRootLoaderInterface $loader,
        private EventStoreInterface $eventStore,
        private EventBusInterface $eventBus,
        private EventStreamDecoratorInterface $streamDecorator,
        private ?Snapshotter $snapshotter = null,
    ) {
    }

    /**
     * @return TAggregate
     *
     * @throws EventSourcingRepositoryException
     */
    final protected function load(Uuid $id): AggregateRootInterface
    {
        try {
            return ($this->loader)($id);
        } catch (\Throwable $e) {
            throw EventSourcingRepositoryException::fromThrowable($e);
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

            $this->snapshotter?->__invoke($aggregateRoot);
        } catch (\Throwable $e) {
            throw EventSourcingRepositoryException::fromThrowable($e);
        }
    }
}

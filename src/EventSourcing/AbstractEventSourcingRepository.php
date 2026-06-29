<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\Uuid;

/**
 * @template TAggregate of AggregateRootInterface
 */
abstract readonly class AbstractEventSourcingRepository
{
    /**
     * Reads and writes are each delegated to a seam — a loader and a register —
     * so the repository neither reads the stream nor appends, publishes or
     * snapshots itself. Both seams are plain by default (event-store loader and
     * register) and independently decorated by snapshotting when configured, so
     * loading resumes from a snapshot and saving captures one.
     *
     * @param AggregateRootLoaderInterface<TAggregate>   $loader
     * @param AggregateRootRegisterInterface<TAggregate> $register
     */
    public function __construct(
        private AggregateRootLoaderInterface $loader,
        private AggregateRootRegisterInterface $register,
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
            ($this->register)($aggregateRoot);
        } catch (\Throwable $e) {
            throw EventSourcingRepositoryException::fromThrowable($e);
        }
    }
}

<?php

declare(strict_types=1);

namespace Shared\EventSourcing\Loader;

use Shared\Domain\Uuid;
use Shared\EventSourcing\AggregateRootFactoryInterface;
use Shared\EventSourcing\AggregateRootInterface;
use Shared\EventSourcing\AggregateRootLoaderInterface;
use Shared\EventStore\EventStoreInterface;

/**
 * The base loader: fetches the aggregate's full stream from the event store and
 * hands it to the factory to rebuild. This is the plain path — no snapshot — and
 * the inner loader that snapshotting wraps.
 *
 * @template-covariant TAggregate of AggregateRootInterface
 *
 * @implements AggregateRootLoaderInterface<TAggregate>
 */
final readonly class EventStoreAggregateRootLoader implements AggregateRootLoaderInterface
{
    /**
     * @param AggregateRootFactoryInterface<TAggregate> $factory
     */
    public function __construct(
        private EventStoreInterface $eventStore,
        private AggregateRootFactoryInterface $factory,
    ) {
    }

    /**
     * @return TAggregate
     */
    #[\Override]
    public function __invoke(Uuid $id): AggregateRootInterface
    {
        return ($this->factory)($this->eventStore->load($id));
    }
}

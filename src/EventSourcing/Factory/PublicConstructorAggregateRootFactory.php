<?php

declare(strict_types=1);

namespace Shared\EventSourcing\Factory;

use Shared\Domain\DomainEventStream;
use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;
use Shared\EventSourcing\AggregateRootFactoryInterface;
use Shared\EventSourcing\AggregateRootInterface;

/**
 * Aggregate factory that builds the aggregate through its public
 * constructor and replays the stream into it.
 *
 * @template-covariant TAggregate of AggregateRootInterface
 *
 * @implements AggregateRootFactoryInterface<TAggregate>
 */
final readonly class PublicConstructorAggregateRootFactory implements AggregateRootFactoryInterface
{
    /**
     * @param class-string<TAggregate> $aggregateRoot
     */
    public function __construct(
        private string $aggregateRoot,
    ) {
    }

    /**
     * @return TAggregate
     */
    #[\Override]
    public function __invoke(DomainEventStream $stream): AggregateRootInterface
    {
        $aggregateRoot = new $this->aggregateRoot();

        if (!$aggregateRoot instanceof AbstractEventSourcedAggregateRoot) {
            throw InvalidAggregateRootException::mustExtendAggregateRoot($this->aggregateRoot);
        }

        $aggregateRoot->initialize($stream);

        /* @var TAggregate $aggregateRoot */
        return $aggregateRoot;
    }
}

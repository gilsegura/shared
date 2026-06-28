<?php

declare(strict_types=1);

namespace Shared\EventSourcing\Factory;

use Shared\Domain\DomainEventStream;
use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;
use Shared\EventSourcing\AggregateRootFactoryInterface;
use Shared\EventSourcing\AggregateRootInterface;

/**
 * Aggregate factory that builds the aggregate without invoking its
 * constructor and replays the stream into it.
 *
 * @template-covariant TAggregate of AggregateRootInterface
 *
 * @implements AggregateRootFactoryInterface<TAggregate>
 */
final readonly class ReflectionAggregateRootFactory implements AggregateRootFactoryInterface
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
        $class = new \ReflectionClass($this->aggregateRoot);
        $aggregateRoot = $class->newInstanceWithoutConstructor();

        if (!$aggregateRoot instanceof AbstractEventSourcedAggregateRoot) {
            throw InvalidAggregateRootException::mustExtendAggregateRoot($this->aggregateRoot);
        }

        $aggregateRoot->initialize($stream);

        /* @var TAggregate $aggregateRoot */
        return $aggregateRoot;
    }
}

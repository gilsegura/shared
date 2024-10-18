<?php

declare(strict_types=1);

namespace Shared\EventSourcing\Factory;

use Shared\Domain\DomainEventStream;
use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;
use Shared\EventSourcing\AggregateRootFactoryInterface;
use Shared\EventSourcing\AggregateRootInterface;

final readonly class PublicConstructorAggregateRootFactory implements AggregateRootFactoryInterface
{
    public function __construct(
        private string $aggregateRootFQCN,
    ) {
    }

    #[\Override]
    public function create(DomainEventStream $stream): AggregateRootInterface
    {
        /** @var AbstractEventSourcedAggregateRoot $aggregateRoot */
        $aggregateRoot = new $this->aggregateRootFQCN();

        if (!$aggregateRoot instanceof AbstractEventSourcedAggregateRoot) {
            throw new \LogicException('AggregateRoot must extends AggregateRootInterface');
        }

        $aggregateRoot->initialize($stream);

        return $aggregateRoot;
    }
}

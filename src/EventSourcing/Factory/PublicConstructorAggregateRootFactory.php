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
    public function __invoke(DomainEventStream $stream): AggregateRootInterface
    {
        $aggregateRoot = new $this->aggregateRootFQCN();

        if (!$aggregateRoot instanceof AbstractEventSourcedAggregateRoot) {
            throw new \InvalidArgumentException(\sprintf('Class "%s" must extend %s.', $this->aggregateRootFQCN, AbstractEventSourcedAggregateRoot::class));
        }

        $aggregateRoot->initialize($stream);

        return $aggregateRoot;
    }
}

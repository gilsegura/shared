<?php

declare(strict_types=1);

namespace Shared\EventSourcing\Factory;

use ProxyAssert\Assertion;
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
        /** @var AbstractEventSourcedAggregateRoot $aggregateRoot */
        $aggregateRoot = new $this->aggregateRootFQCN();

        Assertion::isInstanceOf($aggregateRoot, AbstractEventSourcedAggregateRoot::class);

        $aggregateRoot->initialize($stream);

        return $aggregateRoot;
    }
}

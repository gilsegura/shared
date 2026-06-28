<?php

declare(strict_types=1);

namespace Shared\Tests\EventSourcing\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shared\Domain\DomainEventStream;
use Shared\Domain\Uuid;
use Shared\EventSourcing\AggregateRootInterface;
use Shared\EventSourcing\Factory\InvalidAggregateRootException;
use Shared\EventSourcing\Factory\PublicConstructorAggregateRootFactory;

final class InvalidAggregateRootExceptionTest extends TestCase
{
    #[Test]
    public function it_is_raised_when_the_class_is_not_an_event_sourced_aggregate(): void
    {
        self::expectException(InvalidAggregateRootException::class);

        /** @var PublicConstructorAggregateRootFactory<NotAnEventSourcedAggregate> $factory */
        $factory = new PublicConstructorAggregateRootFactory(NotAnEventSourcedAggregate::class);

        $factory(new DomainEventStream());
    }
}

/**
 * Implements the aggregate contract but is not event-sourced (does not extend
 * AbstractEventSourcedAggregateRoot), so the factory must reject it.
 */
final class NotAnEventSourcedAggregate implements AggregateRootInterface
{
    #[\Override]
    public function id(): Uuid
    {
        return new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac');
    }

    #[\Override]
    public function uncommittedEvents(): DomainEventStream
    {
        return new DomainEventStream();
    }
}

<?php

declare(strict_types=1);

namespace Shared\Tests\EventHandling;

use PHPUnit\Framework\TestCase;
use Shared\Domain\DomainEventInterface;
use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;
use Shared\Domain\Uuid;
use Shared\EventHandling\EventBusException;
use Shared\EventHandling\EventListenerInterface;
use Shared\EventHandling\SimpleEventBus;
use Shared\Tests\InMemoryCollector;

final class SimpleEventBusTest extends TestCase
{
    public function test_must_throw_an_event_bus_exception(): void
    {
        self::expectException(EventBusException::class);

        $message = DomainMessage::record(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
            0,
            Metadata::empty(),
            new AnEvent()
        );

        $bus = new SimpleEventBus();
        $bus->subscribe(new ThrowableEventListener());

        $bus->publish(new DomainEventStream($message));
    }

    public function test_must_publish_a_message(): void
    {
        $message = DomainMessage::record(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
            0,
            Metadata::empty(),
            new AnEvent()
        );

        $bus = new SimpleEventBus();
        $collector = new InMemoryCollector();
        $bus->subscribe(new EventListener($collector));

        $bus->publish(new DomainEventStream($message));

        /** @var DomainMessage[] $messages */
        $messages = $collector->objects();
        $message = $messages[0];
        $event = $message->payload;

        self::assertInstanceOf(AnEvent::class, $event);
    }
}

final readonly class AnEvent implements DomainEventInterface
{
    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self();
    }

    #[\Override]
    public function serialize(): array
    {
        return [];
    }
}

final readonly class EventListener implements EventListenerInterface
{
    public function __construct(
        private InMemoryCollector $collector,
    ) {
    }

    #[\Override]
    public function handle(DomainMessage $message): void
    {
        $this->collector->collect($message);
    }
}

final readonly class ThrowableEventListener implements EventListenerInterface
{
    #[\Override]
    public function handle(DomainMessage $message): void
    {
        throw new \Exception();
    }
}

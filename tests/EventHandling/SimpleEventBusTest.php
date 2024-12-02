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

        $bus = new SimpleEventBus(new ThrowableEventListener());

        $bus->__invoke(new DomainEventStream($message));
    }

    public function test_must_publish_a_message(): void
    {
        $message = DomainMessage::record(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
            0,
            Metadata::empty(),
            new AnEvent()
        );

        $collector = new InMemoryEventCollector();
        $bus = new SimpleEventBus($collector);

        $bus->__invoke(new DomainEventStream($message));

        $messages = $collector->messages();
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

final readonly class ThrowableEventListener implements EventListenerInterface
{
    #[\Override]
    public function __invoke(DomainMessage $message): void
    {
        throw new \Exception();
    }
}

final class InMemoryEventCollector implements EventListenerInterface
{
    /** @var DomainMessage[] */
    private array $messages = [];

    #[\Override]
    public function __invoke(DomainMessage $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * @return DomainMessage[]
     */
    public function messages(): array
    {
        $messages = $this->messages;

        $this->messages = [];

        return $messages;
    }
}

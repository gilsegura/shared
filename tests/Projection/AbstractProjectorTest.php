<?php

declare(strict_types=1);

namespace Shared\Tests\Projection;

use PHPUnit\Framework\TestCase;
use Serializer\SerializableInterface;
use Shared\Domain\DomainEventInterface;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;
use Shared\Domain\Uuid;
use Shared\Projection\AbstractProjector;

final class AbstractProjectorTest extends TestCase
{
    public function test_must_apply_specific_event_when_method_exists(): void
    {
        $collector = new Collector();
        $projector = new TestProjector($collector);

        $projector(DomainMessage::record(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
            0,
            Metadata::empty(),
            new EventWasOccurred()
        ));

        /** @var DomainEventInterface[] $events */
        $events = $collector->objects();
        $event = $events[0];

        self::assertInstanceOf(EventWasOccurred::class, $event);
    }

    public function test_must_apply_specific_event_when_method_not_exists(): void
    {
        $collector = new Collector();
        $projector = new TestProjector($collector);

        $projector(DomainMessage::record(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
            0,
            Metadata::empty(),
            new AnotherEventWasOccurred()
        ));

        /** @var DomainEventInterface[] $events */
        $events = $collector->objects();

        self::assertEmpty($events);
    }
}

final readonly class TestProjector extends AbstractProjector
{
    public function __construct(
        private Collector $collector,
    ) {
    }

    protected function applyEventWasOccurred(EventWasOccurred $event): void
    {
        $this->collector->collect($event);
    }
}

/**
 * @implements SerializableInterface<array{}>
 */
final readonly class EventWasOccurred implements DomainEventInterface, SerializableInterface
{
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self();
    }

    #[\Override]
    public function serialize(): array
    {
        return [];
    }
}

/**
 * @implements SerializableInterface<array{}>
 */
final readonly class AnotherEventWasOccurred implements DomainEventInterface, SerializableInterface
{
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self();
    }

    #[\Override]
    public function serialize(): array
    {
        return [];
    }
}

final class Collector
{
    /** @var object[] */
    private array $objects = [];

    public function collect(object $object): void
    {
        $this->objects[] = $object;
    }

    /**
     * @return object[]
     */
    public function objects(): array
    {
        return $this->objects;
    }
}

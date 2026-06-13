<?php

declare(strict_types=1);

namespace Shared\Tests\EventSourcing;

use PHPUnit\Framework\TestCase;
use Serializer\SerializableInterface;
use Shared\Domain\DateTimeImmutable;
use Shared\Domain\DomainEventInterface;
use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;
use Shared\Domain\Uuid;
use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;

final class EventSourcedAggregateRootTest extends TestCase
{
    public function test_must_apply_specific_event_when_method_exists(): void
    {
        $aggregateRoot = EventSourcedAggregateRoot::create(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
        );

        $stream = $aggregateRoot->uncommittedEvents();
        $messages = $stream->messages;
        $message = $messages[0];
        $event = $message->payload;

        self::assertInstanceOf(DomainMessage::class, $message);
        self::assertInstanceOf(EventSourcedAggregateRootWasCreated::class, $event);
        self::assertSame($aggregateRoot->id(), $message->id);
        self::assertSame($aggregateRoot->playhead(), $message->playhead);
    }

    public function test_must_apply_specific_event_when_method_not_exists(): void
    {
        $aggregateRoot = new EventSourcedAggregateRoot();
        $aggregateRoot->initialize(new DomainEventStream(DomainMessage::record(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
            0,
            Metadata::empty(),
            new EventSourcedAggregateRootWasCreated(
                new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
                DateTimeImmutable::now()
            )
        )));

        $aggregateRoot->baz();

        $stream = $aggregateRoot->uncommittedEvents();
        $messages = $stream->messages;
        $message = $messages[0];
        $event = $message->payload;

        self::assertInstanceOf(DomainMessage::class, $message);
        self::assertInstanceOf(EventSourcedAggregateRootWasBazed::class, $event);
        self::assertSame($aggregateRoot->id(), $message->id);
        self::assertSame($aggregateRoot->playhead(), $message->playhead);
    }
}

final class EventSourcedAggregateRoot extends AbstractEventSourcedAggregateRoot
{
    private Uuid $id;

    private DateTimeImmutable $createdAt;

    public static function create(
        Uuid $id,
    ): self {
        $aggregateRoot = new self();

        $aggregateRoot->apply(new EventSourcedAggregateRootWasCreated(
            $id,
            DateTimeImmutable::now()
        ));

        return $aggregateRoot;
    }

    protected function applyEventSourcedAggregateRootWasCreated(EventSourcedAggregateRootWasCreated $event): void
    {
        $this->id = $event->id;
        $this->createdAt = $event->createdAt;
    }

    public function baz(): void
    {
        $this->apply(new EventSourcedAggregateRootWasBazed(
            $this->id,
            DateTimeImmutable::now()
        ));
    }

    #[\Override]
    public function id(): Uuid
    {
        return $this->id;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}

/**
 * @implements SerializableInterface<array{
 *     id: string,
 *     created_at: string
 * }>
 */
final readonly class EventSourcedAggregateRootWasCreated implements DomainEventInterface, SerializableInterface
{
    public function __construct(
        public Uuid $id,
        public DateTimeImmutable $createdAt,
    ) {
    }

    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self(
            new Uuid($attributes['id']),
            new DateTimeImmutable($attributes['created_at']),
        );
    }

    #[\Override]
    public function serialize(): array
    {
        return [
            'id' => $this->id->uuid,
            'created_at' => $this->createdAt->dateTime,
        ];
    }
}

/**
 * @implements SerializableInterface<array{
 *     id: string,
 *     updated_at: string
 * }>
 */
final readonly class EventSourcedAggregateRootWasBazed implements DomainEventInterface, SerializableInterface
{
    public function __construct(
        public Uuid $id,
        public DateTimeImmutable $updatedAt,
    ) {
    }

    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self(
            new Uuid($attributes['id']),
            new DateTimeImmutable($attributes['updated_at']),
        );
    }

    #[\Override]
    public function serialize(): array
    {
        return [
            'id' => $this->id->uuid,
            'updated_at' => $this->updatedAt->dateTime,
        ];
    }
}

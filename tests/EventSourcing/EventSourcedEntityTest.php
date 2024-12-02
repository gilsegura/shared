<?php

declare(strict_types=1);

namespace Shared\Tests\EventSourcing;

use PHPUnit\Framework\TestCase;
use ProxyAssert\Assertion;
use Shared\Domain\DateTimeImmutable;
use Shared\Domain\DomainEventInterface;
use Shared\Domain\DomainMessage;
use Shared\Domain\Uuid;
use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;
use Shared\EventSourcing\AbstractEventSourcedEntity;

final class EventSourcedEntityTest extends TestCase
{
    public function test_must_apply_specific_event_when_method_exists(): void
    {
        $aggregateRoot = AnAggregateRoot::create(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
        );

        $aggregateRoot->uncommittedEvents();

        $aggregateRoot->attach(new Uuid('c467bd14-4265-41f5-9101-5df03595e2a6'));

        $stream = $aggregateRoot->uncommittedEvents();
        $messages = $stream->messages;
        $message = $messages[0];
        $event = $message->payload;

        self::assertInstanceOf(DomainMessage::class, $message);
        self::assertInstanceOf(AnAggregateRootWasAttached::class, $event);
        self::assertSame($aggregateRoot->id(), $message->id);
        self::assertSame($aggregateRoot->playhead(), $message->playhead);

        $aggregateRoot->bazAttachment(new Uuid('c467bd14-4265-41f5-9101-5df03595e2a6'));

        $stream = $aggregateRoot->uncommittedEvents();
        $messages = $stream->messages;
        $message = $messages[0];
        $event = $message->payload;

        self::assertInstanceOf(DomainMessage::class, $message);
        self::assertInstanceOf(AnAggregatedEntityWasBazed::class, $event);
        self::assertSame($aggregateRoot->id(), $message->id);
        self::assertSame($aggregateRoot->playhead(), $message->playhead);
    }
}

final class AnAggregateRoot extends AbstractEventSourcedAggregateRoot
{
    private Uuid $id;

    private DateTimeImmutable $createdAt;

    /** @var AnAggregatedEntity[] */
    private array $anAggregatedEntities = [];

    public static function create(
        Uuid $id,
    ): self {
        $aggregateRoot = new self();

        $aggregateRoot->apply(new AnAggregateRootWasCreated(
            $id,
            DateTimeImmutable::now()
        ));

        return $aggregateRoot;
    }

    protected function applyAnAggregateRootWasCreated(AnAggregateRootWasCreated $event): void
    {
        $this->id = $event->id;
        $this->createdAt = $event->createdAt;
    }

    public function attach(
        Uuid $id,
    ): void {
        $this->apply(new AnAggregateRootWasAttached(
            $this->id,
            $id,
            DateTimeImmutable::now()
        ));
    }

    protected function applyAnAggregateRootWasAttached(AnAggregateRootWasAttached $event): void
    {
        $this->anAggregatedEntities[] = new AnAggregatedEntity(
            $event->anAggregateRootId,
            $event->id,
            $event->updatedAt
        );
    }

    public function bazAttachment(Uuid $id): void
    {
        $attachments = array_filter($this->anAggregatedEntities, static fn (AnAggregatedEntity $attachment) => $id->equals($attachment->id()));

        if ([] === $attachments) {
            return;
        }

        $attachments[0]->baz();
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

    #[\Override]
    protected function childEntities(): array
    {
        return [...$this->anAggregatedEntities];
    }
}

final readonly class AnAggregateRootWasCreated implements DomainEventInterface
{
    public function __construct(
        public Uuid $id,
        public DateTimeImmutable $createdAt,
    ) {
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'created_at');

        return new self(
            new Uuid($data['id']),
            new DateTimeImmutable($data['created_at'])
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

final readonly class AnAggregateRootWasAttached implements DomainEventInterface
{
    public function __construct(
        public Uuid $anAggregateRootId,
        public Uuid $id,
        public DateTimeImmutable $updatedAt,
    ) {
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        Assertion::keyExists($data, 'an_aggregate_root_id');
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'updated_at');

        return new self(
            new Uuid($data['an_aggregate_root_id']),
            new Uuid($data['id']),
            new DateTimeImmutable($data['updated_at'])
        );
    }

    #[\Override]
    public function serialize(): array
    {
        return [
            'an_aggregate_root_id' => $this->anAggregateRootId->uuid,
            'id' => $this->id->uuid,
            'updated_at' => $this->updatedAt->dateTime,
        ];
    }
}

final class AnAggregatedEntity extends AbstractEventSourcedEntity
{
    private Uuid $anAggregateRootId;

    private Uuid $id;

    private DateTimeImmutable $createdAt;

    public function __construct(
        Uuid $anAggregateRootId,
        Uuid $id,
        DateTimeImmutable $createdAt,
    ) {
        $this->anAggregateRootId = $anAggregateRootId;
        $this->id = $id;
        $this->createdAt = $createdAt;
    }

    public function baz(): void
    {
        $this->apply(new AnAggregatedEntityWasBazed(
            $this->id,
            DateTimeImmutable::now()
        ));
    }

    public function anAggregateRootId(): Uuid
    {
        return $this->anAggregateRootId;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}

final readonly class AnAggregatedEntityWasBazed implements DomainEventInterface
{
    public function __construct(
        public Uuid $id,
        public DateTimeImmutable $updatedAt,
    ) {
    }

    #[\Override]
    public static function deserialize(array $data): self
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'updated_at');

        return new self(
            new Uuid($data['id']),
            new DateTimeImmutable($data['updated_at'])
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

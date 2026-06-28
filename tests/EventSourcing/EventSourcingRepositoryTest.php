<?php

declare(strict_types=1);

namespace Shared\Tests\EventSourcing;

use PHPUnit\Framework\TestCase;
use Serializer\SerializableInterface;
use Shared\Domain\DateTimeImmutable;
use Shared\Domain\DomainEventInterface;
use Shared\Domain\Uuid;
use Shared\EventHandling\SimpleEventBus;
use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;
use Shared\EventSourcing\AbstractEventSourcingRepository;
use Shared\EventSourcing\EventSourcingRepositoryException;
use Shared\EventSourcing\Factory\PublicConstructorAggregateRootFactory;
use Shared\EventSourcing\Loader\EventStoreAggregateRootLoader;
use Shared\EventSourcing\MetadataEnricher\MetadataEnrichingEventStreamDecorator;
use Shared\EventStore\StreamNotFoundException;
use Shared\Exception\NotFoundException;
use Shared\Tests\EventStore\InMemoryEventStore;

final class EventSourcingRepositoryTest extends TestCase
{
    public function test_must_throw_event_sourcing_repository_exception_when_aggregate_root_is_not_stored(): void
    {
        self::expectException(NotFoundException::class);

        $eventStore = new InMemoryEventStore();

        $store = new AnotherEventSourcedAggregateRootRepository(
            new EventStoreAggregateRootLoader(
                $eventStore,
                new PublicConstructorAggregateRootFactory(AnotherEventSourcedAggregateRoot::class),
            ),
            $eventStore,
            new SimpleEventBus(),
            new MetadataEnrichingEventStreamDecorator(),
        );

        $store->get(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'));
    }

    public function test_must_get_aggregate_root_when_aggregate_root_is_stored(): void
    {
        $aggregateRoot = AnotherEventSourcedAggregateRoot::create(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
        );

        $eventStore = new InMemoryEventStore();

        $store = new AnotherEventSourcedAggregateRootRepository(
            new EventStoreAggregateRootLoader(
                $eventStore,
                new PublicConstructorAggregateRootFactory(AnotherEventSourcedAggregateRoot::class),
            ),
            $eventStore,
            new SimpleEventBus(),
            new MetadataEnrichingEventStreamDecorator(),
        );

        $store->store($aggregateRoot);

        $aggregateRoot = $store->get(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'));

        self::assertInstanceOf(AnotherEventSourcedAggregateRoot::class, $aggregateRoot);
    }
}

/**
 * @extends AbstractEventSourcingRepository<AnotherEventSourcedAggregateRoot>
 */
final readonly class AnotherEventSourcedAggregateRootRepository extends AbstractEventSourcingRepository
{
    /**
     * @throws EventSourcingRepositoryException
     * @throws NotFoundException
     */
    public function get(Uuid $id): AnotherEventSourcedAggregateRoot
    {
        try {
            return $this->load($id);
        } catch (EventSourcingRepositoryException $e) {
            if ($e->getPrevious() instanceof StreamNotFoundException) {
                throw new NotFoundException($e->getMessage(), $e->getCode(), previous: $e);
            }

            throw $e;
        }
    }

    /**
     * @throws EventSourcingRepositoryException
     */
    public function store(AnotherEventSourcedAggregateRoot $aggregateRoot): void
    {
        $this->save($aggregateRoot);
    }
}

final class AnotherEventSourcedAggregateRoot extends AbstractEventSourcedAggregateRoot
{
    private Uuid $id;

    private DateTimeImmutable $createdAt;

    public static function create(
        Uuid $id,
    ): self {
        $foo = new self();

        $foo->apply(new AnotherEventSourcedAggregateRootWasCreated(
            $id,
            DateTimeImmutable::now()
        ));

        return $foo;
    }

    protected function applyAnotherEventSourcedAggregateRootWasCreated(AnotherEventSourcedAggregateRootWasCreated $event): void
    {
        $this->id = $event->id;
        $this->createdAt = $event->createdAt;
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
final readonly class AnotherEventSourcedAggregateRootWasCreated implements DomainEventInterface, SerializableInterface
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

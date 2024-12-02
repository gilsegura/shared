<?php

declare(strict_types=1);

namespace Shared\Tests\EventSourcing;

use PHPUnit\Framework\TestCase;
use ProxyAssert\Assertion;
use Shared\Domain\DateTimeImmutable;
use Shared\Domain\DomainEventInterface;
use Shared\Domain\Uuid;
use Shared\EventHandling\SimpleEventBus;
use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;
use Shared\EventSourcing\AbstractEventSourcingRepository;
use Shared\EventSourcing\EventSourcingRepositoryException;
use Shared\EventSourcing\Factory\PublicConstructorAggregateRootFactory;
use Shared\EventSourcing\MetadataEnricher\MetadataEnrichingEventStreamDecorator;
use Shared\EventStore\StreamNotFoundException;
use Shared\Exception\NotFoundException;
use Shared\Tests\EventStore\InMemoryEventStore;

final class EventSourcingRepositoryTest extends TestCase
{
    public function test_must_throw_event_sourcing_repository_exception_when_aggregate_root_is_not_stored(): void
    {
        self::expectException(NotFoundException::class);

        $store = new AnotherEventSourcedAggregateRootRepository(
            new InMemoryEventStore(),
            new SimpleEventBus(),
            new MetadataEnrichingEventStreamDecorator(),
            new PublicConstructorAggregateRootFactory(AnotherEventSourcedAggregateRoot::class)
        );

        $store->get(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'));
    }

    public function test_must_get_aggregate_root_when_aggregate_root_is_stored(): void
    {
        $aggregateRoot = AnotherEventSourcedAggregateRoot::create(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
        );

        $store = new AnotherEventSourcedAggregateRootRepository(
            new InMemoryEventStore(),
            new SimpleEventBus(),
            new MetadataEnrichingEventStreamDecorator(),
            new PublicConstructorAggregateRootFactory(AnotherEventSourcedAggregateRoot::class)
        );

        $store->store($aggregateRoot);

        $aggregateRoot = $store->get(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'));

        self::assertInstanceOf(AnotherEventSourcedAggregateRoot::class, $aggregateRoot);
    }
}

final readonly class AnotherEventSourcedAggregateRootRepository extends AbstractEventSourcingRepository
{
    /**
     * @throws EventSourcingRepositoryException
     * @throws NotFoundException
     */
    public function get(Uuid $id, ?int $playhead = null): AnotherEventSourcedAggregateRoot
    {
        try {
            /* @phpstan-ignore return.type */
            return $this->load($id, $playhead);
        } catch (EventSourcingRepositoryException $e) {
            if ($e->getPrevious() instanceof StreamNotFoundException) {
                throw NotFoundException::throwable($e);
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

final readonly class AnotherEventSourcedAggregateRootWasCreated implements DomainEventInterface
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

# SHARED COMPONENT

[![tests](https://github.com/gilsegura/shared/actions/workflows/tests.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/tests.yaml)
[![codecov](https://codecov.io/github/gilsegura/shared/graph/badge.svg)](https://codecov.io/github/gilsegura/shared)
[![static analysis](https://github.com/gilsegura/shared/actions/workflows/static-analysis.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/static-analysis.yaml)
[![coding standards](https://github.com/gilsegura/shared/actions/workflows/coding-standards.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/coding-standards.yaml)

Framework-agnostic building blocks for Domain-Driven Design, Event Sourcing and
CQRS. It gives you the domain primitives (value objects, aggregates, events),
the write side (event store, event bus, repositories), the read side (read
models, projectors), and a small typed criteria/query DSL — without tying you
to any framework. The Symfony/Doctrine integration lives in the companion
`gilsegura/shared-bundle`.

## Installation

```bash
composer require gilsegura/shared
```

Requires PHP 8.4+, `ext-mbstring`, `gilsegura/serializer` and `ramsey/uuid`.

## What's inside

### Domain primitives

Small, immutable value objects with enforced invariants: `Uuid`, `Email`,
`NotEmptyString`, `HashedPassword`, `Metadata`, `DateTimeImmutable`. Each
validates on construction and fails with an `InvalidInputException` that names
the problem and includes the offending value:

```php
use Shared\Domain\Email;
use Shared\Domain\Uuid;

$id = Uuid::uuid4();
$email = new Email('ada@example.com');   // throws InvalidInputException if invalid
$email->equals(new Email('ADA@example.com')); // true — emails compare case-insensitively
```

Events are plain objects implementing `DomainEventInterface`; a recorded event
is wrapped in a `DomainMessage` (id, playhead, metadata, payload, timestamp).

### Event-sourced aggregates

Extend `AbstractEventSourcedAggregateRoot`: apply events, let the playhead and
the uncommitted-event stream be tracked for you, and rebuild state from history
through `apply{EventName}` methods resolved by convention.

```php
final class Account extends AbstractEventSourcedAggregateRoot
{
    public function withdraw(Money $amount): void
    {
        $this->apply(new MoneyWithdrawn($this->id, $amount));
    }

    protected function applyMoneyWithdrawn(MoneyWithdrawn $event): void
    {
        $this->balance = $this->balance->subtract($event->amount);
    }
}
```

Child entities are supported through `AbstractEventSourcedEntity` and
`childEntities()`, so events are handled recursively across the aggregate.

### Write side

- `EventStoreInterface` / `EventStoreManagerInterface` — append and load streams.
- `SimpleEventBus` — publishes domain messages to listeners, with a reentrancy
  guard so events emitted while publishing are queued and drained in order.
- `AbstractEventSourcingRepository` — load/save aggregates from the event store.
- `MetadataEnrichingEventStreamDecorator` + `MetadataEnricherInterface` — attach
  cross-cutting metadata (correlation, causation, actor) as events are stored.
- `SequentialUpcasterChain` / `UpcastingEventStore` — evolve old event shapes on
  read through an upcaster chain.

### Read side

- `ReadModelInterface` / `ReadModelRepositoryInterface` — your projections.
- `AbstractProjector` — build read models by reacting to domain events.
- `Replayer` — rebuild projections from the event store.

### Command & query buses

`CommandBusInterface`, `QueryBusInterface` and their handler interfaces give you
a typed CQRS seam. Queries are built with a small immutable DSL.

### The criteria / query DSL

Queries are assembled fluently and immutably. `SingleResultQuery` (findOne) and
`CollectionQuery` (findMany) build a criteria tree with `where`, `andX`, `orX`,
and — for collections — `orderBy`, `withOffset`, `withLimit`. Each operation
returns a new builder, so a query is safe to share and refine:

```php
$query = FindPosts::create()
    ->andX(fn (FindPosts $q) => $q->publishedOnly()->byAuthor($authorId))
    ->orderBy(OrderX::desc('createdAt'))
    ->withLimit(20);
```

The criteria (`AndX`, `OrX`, `Comparison`, `OrderX`…) map to a backend-agnostic
expression tree that the infrastructure layer (e.g. Doctrine in
`shared-bundle`) translates to a real query.

## Design notes

- **Immutable by default.** Value objects are `readonly`; the query builders are
  effectively immutable (no setters, every operation returns a clone).
- **Framework-agnostic.** Nothing here depends on a framework; the bus, store
  and repository are interfaces you wire to your infrastructure.
- **Errors are typed.** A small exception hierarchy (`InvalidInputException`,
  `NotFoundException`, `ConflictException`, `ForbiddenException`,
  `UnauthorizedException`, `InfrastructureException`, `UnexpectedException`)
  maps cleanly onto transport-level responses.
- **PHP 8.4, strictly analysed.** Built and checked under PHPStan `max` with
  strict rules.

## License

MIT. See [LICENSE](LICENSE).

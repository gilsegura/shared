# SHARED COMPONENT

[![tests](https://github.com/gilsegura/shared/actions/workflows/tests.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/tests.yaml)
[![codecov](https://codecov.io/github/gilsegura/shared/graph/badge.svg)](https://codecov.io/github/gilsegura/shared)
[![static analysis](https://github.com/gilsegura/shared/actions/workflows/static-analysis.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/static-analysis.yaml)
[![coding standards](https://github.com/gilsegura/shared/actions/workflows/coding-standards.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/coding-standards.yaml)

A lightweight, framework-agnostic infrastructure component for building CQRS and
Event Sourcing applications in PHP.

It provides the building blocks for event-sourced aggregates, an event store
abstraction with upcasting and replaying, read-model projections, a
criteria-based query layer, command and query buses, a set of immutable domain
value objects, and a semantic exception hierarchy. Every layer is an interface
first, so applications wire their own infrastructure (Doctrine, Messenger, etc.)
at the boundary.

## Features

* PHP 8.5+
* Event Sourcing building blocks: aggregates, entities, domain messages and streams
* Event store abstraction with an upcasting layer and a replaying layer
* Read-model projections and a generic repository contract
* Command and query buses (CQRS) with type-safe handlers
* Criteria-based query layer with a fluent query builder
* Immutable domain value objects (`Uuid`, `Email`, `DateTimeImmutable`, ...)
* Serializable contracts backed by `gilsegura/serializer`
* Semantic, transport-agnostic exception hierarchy
* Strong static typing with generics
* Immutable design throughout
* Framework agnostic

## Installation

```bash
composer require gilsegura/shared
```

## Architecture

The component is organised into focused namespaces under `Shared\`:

| Namespace | Responsibility |
| --- | --- |
| `Domain` | Value objects, domain events, metadata, the domain message envelope |
| `EventSourcing` | Aggregate roots, entities, the event-sourcing repository |
| `EventStore` | Event store abstraction, manager and visitor |
| `EventHandling` | Event bus and listeners |
| `Upcasting` | Event upcasting chain over a decorated store |
| `Replaying` | Re-dispatching stored events to listeners |
| `ReadModel` | Read models, projectors and their repository |
| `CommandHandling` | Command and query buses, handlers and messages |
| `Query` | Fluent query builders on top of `Criteria` |
| `Criteria` | Composable criteria and ordering expressions |
| `Specification` | Specification base class |
| `Exception` | Semantic, transport-agnostic exception hierarchy |

## Domain

### Value objects

The `Domain` namespace ships immutable value objects used across the component.
Each is `final readonly` and exposes an `equals()` method for comparison by
value:

* `Uuid` — a UUID, with `Uuid::uuid4()` to generate one and `equals()` to compare.
* `Email` — a validated email address.
* `DateTimeImmutable` — a wrapper with `now()`, `fromTimestamp()`, `toTimestamp()`, `addSeconds()`, `subSeconds()` and `equals()`.
* `NotEmptyString` — a string guaranteed not to be empty.
* `HashedPassword` — a password hash, with `encode()` to hash a plaintext password and `match()` to verify one.

`IdentifiableInterface` marks anything addressable by a `Uuid` through `id()`.

### Domain events

A domain event implements `DomainEventInterface` (a domain marker) together with
`SerializableInterface` from `gilsegura/serializer`. The serialized shape is
declared once, in the class `@implements` tag, which lets static analysis type
every attribute without casts or assertions:

```php
use Serializer\SerializableInterface;
use Shared\Domain\DomainEventInterface;

/**
 * @implements SerializableInterface<array{
 *     id: string,
 *     created_at: string
 * }>
 */
final readonly class OrderWasPlaced implements DomainEventInterface, SerializableInterface
{
    public function __construct(
        public Uuid $id,
        public DateTimeImmutable $createdAt,
    ) {
    }

    public static function deserialize(array $attributes): static
    {
        return new self(
            new Uuid($attributes['id']),
            new DateTimeImmutable($attributes['created_at']),
        );
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->uuid,
            'created_at' => $this->createdAt->dateTime,
        ];
    }
}
```

### Domain message and stream

A `DomainMessage` is the envelope recorded for each event: it carries the
aggregate `id`, the `playhead` (sequence number), `Metadata`, the event
`payload`, a `recordedAt` timestamp and a derived `type` string. Messages are
grouped into an ordered `DomainEventStream`.

### Metadata

`Metadata` is an immutable, serializable bag of key/value pairs attached to a
message. Build one with `Metadata::empty()` or `Metadata::kv($key, $value)`, and
combine two with `merge()`, which returns a new instance:

```php
$metadata = Metadata::kv('correlation_id', $correlationId)
    ->merge(Metadata::kv('user_id', $userId));
```

## Event Sourcing

### Aggregates and entities

An aggregate extends `AbstractEventSourcedAggregateRoot`. It records events by
calling `apply()`, which appends a `DomainMessage` to the uncommitted events and
dispatches the event to a conventionally named `apply<EventName>()` method.
State is rebuilt from a stream with `initialize()`:

```php
final class Order extends AbstractEventSourcedAggregateRoot
{
    private Uuid $id;

    public static function place(Uuid $id): self
    {
        $order = new self();
        $order->apply(new OrderWasPlaced($id, DateTimeImmutable::now()));

        return $order;
    }

    protected function applyOrderWasPlaced(OrderWasPlaced $event): void
    {
        $this->id = $event->id;
    }

    public function id(): Uuid
    {
        return $this->id;
    }
}
```

Child entities extend `AbstractEventSourcedEntity` and are returned from the
aggregate's `childEntities()`, so events propagate recursively through the
aggregate tree. The `apply<EventName>()` resolution is provided by
`ResolvesApplyMethodTrait`.

### Repository

`AbstractEventSourcingRepository` loads and stores aggregates through an
`EventStoreInterface`, reconstituting them with an
`AggregateRootFactoryInterface`. `PublicConstructorAggregateRootFactory` builds
aggregates that expose a public constructor.

### Stream decorators

`EventStreamDecoratorInterface` transforms a stream as it is persisted. The
`MetadataEnricher` decorator (`MetadataEnrichingEventStreamDecorator` plus
`MetadataEnricherInterface`) enriches each message's metadata before it is
stored.

## Event store

`EventStoreInterface` abstracts stream persistence with `load()` (optionally up
to a given playhead) and `append()`. `EventStoreManagerInterface` adds
`visitEvents()`, which walks events matching a criteria through an
`EventVisitorInterface`. `CallableEventVisitor` adapts a callable into a
visitor.

Loading a missing or already-existing stream raises `StreamNotFoundException`
and `StreamAlreadyExistsException` respectively.

## Upcasting

The `Upcasting` layer migrates stored events to newer shapes on read.
`UpcastingEventStore` decorates an event store and runs each message through an
`UpcasterChainInterface`. `SequentialUpcasterChain` applies a sequence of
`UpcasterInterface` instances, each rewriting a `DomainMessage` into a newer
version. This keeps old events on disk while presenting the current shape to the
domain.

## Replaying

`Replayer` re-reads stored events matching a criteria and re-dispatches them,
which is useful for rebuilding read models or projections from history.

## Event handling

`EventBusInterface` publishes a `DomainEventStream` to registered
`EventListenerInterface` listeners. `SimpleEventBus` is an in-memory
implementation that forwards each message to every subscribed listener.
Listener failures surface as `EventBusException`.

## Read models

A read model implements `ReadModelInterface` (which extends
`IdentifiableInterface`) together with `SerializableInterface`. Read models are
populated by projectors extending `AbstractProjector`, whose
`apply<EventName>()` methods react to incoming messages, and are retrieved
through the generic `ReadModelRepositoryInterface`:

```php
/**
 * @template TReadModel of ReadModelInterface
 */
interface ReadModelRepositoryInterface
{
    /** @return TReadModel */
    public function oneOrException(Uuid $id): ReadModelInterface;

    /** @return TReadModel[] */
    public function findBy(/* criteria, sort, offset, limit */): array;

    /** @param TReadModel $readModel */
    public function save(ReadModelInterface $readModel): void;
}
```

Missing read models raise `ReadModelRepositoryException`.

## Command and query handling (CQRS)

The `CommandHandling` namespace separates writes from reads.

Commands implement `CommandInterface`, are handled by a
`CommandHandlerInterface<TCommand>` and dispatched through `CommandBusInterface`,
which returns nothing.

Queries implement `QueryInterface<TResult>` and are dispatched through
`QueryBusInterface`, which preserves the query's result type. A handler
(`QueryHandlerInterface<TResult, TQuery>`) returns a read model, a list of read
models, or `null` when nothing is found. The bus returns exactly that type:

```php
interface QueryBusInterface
{
    /**
     * @template TResult
     *
     * @param QueryInterface<TResult> $query
     *
     * @return TResult
     */
    public function __invoke(QueryInterface $query): mixed;
}
```

The typical flow is `query → query bus → query handler → repository → read
model` (or `read model[]` for a collection).

## Query layer

Queries are built on top of the `Criteria` layer. `QueryBuilder` is the base
fluent builder; `SingleResultQuery<TResult>` resolves to a single result (or
`null`) and `CollectionQuery<TResult>` resolves to a list, adding ordering,
offset and limit:

```php
$query = SomeCollectionQuery::of()
    ->orderBy($sort)
    ->withOffset(0)
    ->withLimit(20);
```

Each builder method returns a new instance, keeping queries immutable.

## Criteria

The `Criteria` namespace composes query predicates independently of any storage
engine. `CriteriaInterface` exposes an `expr()` returning an
`ExpressionInterface`. Criteria combine through `AndX` and `OrX`, and ordering
is expressed with `OrderX`. Ready-made criteria include `EqId`, `EqEmail`,
`EqPlayhead`, `ByPlayhead` and `ByRecordedAt`. The underlying expression model
lives under `Criteria\Expr` (comparisons, composites, operators and sorting),
letting an infrastructure adapter translate criteria into its own query
language.

## Specifications

`AbstractSpecification` provides a base for the Specification pattern, useful
for encapsulating reusable business rules that can be evaluated against domain
objects.

## Exceptions

The component exposes a semantic exception hierarchy under `Shared\Exception`.
Each category describes the nature of the failure, not its transport
representation:

* `NotFoundException`
* `ConflictException`
* `InvalidInputException`
* `UnauthorizedException`
* `ForbiddenException`
* `UnexpectedException`
* `InfrastructureException`

Exceptions carry no error codes or HTTP semantics. Mapping a category to a
concrete protocol response is left to each application at its boundary.

## Serialization

Domain events, read models and metadata implement
`SerializableInterface<TAttributes>` from `gilsegura/serializer`, where
`TAttributes` is the concrete shape of the serialized array, declared in the
class `@implements` tag. Because the shape is known statically, `deserialize()`
reads its attributes with no casts or assertions. Persisting these objects (for
example through a Doctrine custom type) goes through the serializer facade, which
stores the class name alongside the attributes and restores the exact concrete
type on read.

## Design principles

* **Framework agnostic** — every integration point is an interface; no framework
  is required to use the domain layer.
* **Immutable** — value objects, messages, metadata and queries are
  `readonly` and return new instances on change.
* **Statically typed** — generics flow through repositories, buses and queries
  so results keep their concrete type without manual narrowing.
* **Transport-agnostic errors** — exceptions describe failure categories;
  protocol mapping happens at the application boundary.

## License

MIT. See [LICENSE](LICENSE).

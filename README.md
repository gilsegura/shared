# SHARED
[![tests](https://github.com/gilsegura/shared/actions/workflows/tests.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/tests.yaml)
[![codecov](https://codecov.io/github/gilsegura/shared/graph/badge.svg)](https://codecov.io/github/gilsegura/shared)
[![static analysis](https://github.com/gilsegura/shared/actions/workflows/static-analysis.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/static-analysis.yaml)
[![coding standards](https://github.com/gilsegura/shared/actions/workflows/coding-standards.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/coding-standards.yaml)

Framework-agnostic building blocks for DDD, CQRS and event sourcing. It provides
the domain primitives (value objects, events, aggregates), the command/query and
event bus contracts, the event store and upcasting contracts, a criteria DSL for
querying, and read-model projection support — all as plain PHP, with no
framework dependency. The `gilsegura/shared-bundle` package wires these onto
Symfony, Messenger and Doctrine.

## Installation

```bash
composer require gilsegura/shared
```

## Domain primitives

`Shared\Domain` holds the value objects and the event envelope every other piece
builds on:

- **`Uuid`, `Email`, `HashedPassword`, `NotEmptyString`, `DateTimeImmutable`** —
  immutable value objects with validation and equality.
- **`DomainEventInterface`** — marker for domain events; events are serializable
  so they can be stored and replayed.
- **`DomainMessage`** — wraps a domain event (`payload`) together with the
  aggregate `id`, `playhead`, `Metadata` and `recordedAt`. The `type` is derived
  from the event class. This is the unit the event store persists and the buses
  carry.
- **`DomainEventStream`** — an ordered, iterable stream of `DomainMessage`s.
- **`Metadata`** — arbitrary key/value context attached to a message.

## Command and query handling

`Shared\CommandHandling` defines the CQRS contracts, with no transport opinion:

- **`CommandInterface` / `QueryInterface`** — markers for messages.
- **`CommandHandlerInterface` / `QueryHandlerInterface`** — markers for handlers,
  generic over the message they handle.
- **`CommandBusInterface` / `QueryBusInterface`** — the buses a use case depends
  on. An adapter (e.g. in the bundle) provides the concrete bus.

## Event handling

`Shared\EventHandling` is the synchronous, in-process event bus:

- **`EventListenerInterface`** — a listener invoked with a `DomainMessage`.
- **`EventBusInterface`** — publishes a `DomainEventStream` to its listeners.
- **`SimpleEventBus`** — publishes to its listeners in order, fail-fast: the
  first listener that throws stops the dispatch.

## Event sourcing

`Shared\EventSourcing` turns event streams into aggregates and back:

- **`AbstractEventSourcedAggregateRoot`** — base aggregate. `apply()` records an
  event and routes it to an `applyXxx` method resolved from the event's short
  name; `uncommittedEvents()` returns what to persist; `initialize()` rebuilds
  state from a stream. Child entities are reachable through `childEntities()`.
- **`AbstractEventSourcedEntity`** — same apply mechanics for entities nested
  inside an aggregate.
- **`AbstractEventSourcingRepository`** — loads an aggregate from the event store
  (through a factory) and saves its uncommitted events, publishing them on the
  event bus.
- **`AggregateRootFactoryInterface`** + **`PublicConstructorAggregateRootFactory`**
  — rebuild an aggregate instance from a stream.
- **`EventStreamDecoratorInterface`** + **`MetadataEnricher`** — decorate the
  outgoing stream, e.g. to enrich every message's metadata.

## Event store

`Shared\EventStore` is the persistence contract for streams:

- **`EventStoreInterface`** — `load`/`append` a `DomainEventStream` by aggregate
  id, with `StreamNotFoundException` / `StreamAlreadyExistsException`.
- **`EventStoreManagerInterface`** — visiting/streaming events for replay and
  maintenance.
- **`EventVisitorInterface` / `CallableEventVisitor`** — visit matching events.

## Upcasting

`Shared\Upcasting` keeps old event shapes loadable as the schema evolves:

- **`UpcasterInterface`** — transforms a `DomainMessage` into its newer shape.
- **`SequentialUpcasterChain`** — runs a series of upcasters in order.
- **`UpcastingEventStore`** — decorates an event store so events are upcast as
  they are read. Wrap the real store with this only when you have upcasters;
  without them, use the store directly.

## Criteria

`Shared\Criteria` is a small DSL for expressing filters and ordering in domain
terms, independent of any database:

- Composites **`AndX` / `OrX`** combine comparisons such as **`EqId`**,
  **`EqEmail`**, **`EqPlayhead`**, **`ByPlayhead`**, **`ByRecordedAt`**.
- **`OrderX`** expresses sorting.
- The `Expr` namespace is the lower-level expression tree the comparisons map to.

An infrastructure adapter translates these into a concrete query (the bundle, for
example, converts them to Doctrine criteria).

## Queries and read models

- **`Shared\Query`** — `QueryBuilder` builds a typed query from criteria;
  `CollectionQuery` / `SingleResultQuery` express the expected result shape.
- **`Shared\ReadModel`** — `ReadModelInterface` and `ReadModelRepositoryInterface`
  are the read-side contracts; **`AbstractProjector`** implements
  `EventListenerInterface` and resolves an `applyXxx` method from each event's
  short name, so a projector only writes handlers for the events it reacts to.

## Replaying

`Shared\Replaying\Replayer` visits the events matching a criteria from the store
and feeds them to an event visitor — e.g. to rebuild a read model from history.

## Specifications

`Shared\Specification\AbstractSpecification` is the base for composable domain
specifications (business rules expressed as objects).

## Exceptions

`Shared\Exception` provides a hierarchy mapping domain failures to intent —
`NotFoundException`, `ConflictException`, `ForbiddenException`,
`UnauthorizedException`, `InvalidInputException`, `InfrastructureException`,
`UnexpectedException` — so transport layers can translate them to the right
status without leaking domain details.

## Support

`Shared\Support\ClassName` resolves the short name of a class, used to map events
to their `applyXxx` methods.

## License
MIT. See [LICENSE](LICENSE).
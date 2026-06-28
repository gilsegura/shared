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
  state by replaying a stream onto the aggregate, advancing the playhead from its
  current position (a fresh aggregate replays from the start; one restored from a
  snapshot replays only the events after it). Child entities are reachable
  through `childEntities()`.
- **`AbstractEventSourcedEntity`** — same apply mechanics for entities nested
  inside an aggregate.
- **`AbstractEventSourcingRepository`** — saves an aggregate's uncommitted events
  (appending to the store, publishing on the event bus, and, when configured,
  capturing a snapshot), and loads an aggregate through a loader. It does not read
  the stream itself on the read path.
- **`AggregateRootFactoryInterface`** + **`ReflectionAggregateRootFactory`** /
  **`PublicConstructorAggregateRootFactory`** — turn a given stream into an
  aggregate. A factory only transforms a stream; it does not fetch anything.
- **`AggregateRootLoaderInterface`** — loads a fully rebuilt aggregate by
  id. Unlike a factory, a loader fetches what it needs and produces the
  aggregate, so it takes the id. This is the seam the repository depends on and
  the seam snapshotting decorates.
- **`Loader\EventStoreAggregateRootLoader`** — the base loader: fetches the full
  stream from the event store and hands it to the factory.
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

- **`UpcasterInterface`** — transforms a `DomainMessage` into its newer shape,
  or returns it unchanged when the event is not its concern.
- **`SequentialUpcasterChain`** — runs the message through its upcasters in
  order, each receiving the output of the previous one. With no upcasters the
  message passes through unchanged.
- **`UpcastingEventStore`** — decorates an event store so events are upcast both
  when they are loaded (`load`) and when they are visited (`visitEvents`), so a
  rebuild sees the same current shapes a load produces. An empty chain is a
  transparent pass-through.

## Snapshotting

`Shared\Snapshotting` makes loading long-lived aggregates cheap by skipping the
replay of their whole history. It is opt-in: without it, aggregates always rebuild
from the full stream.

- **`Snapshot`** — a point-in-time capture of an aggregate at a given playhead.
- **`SnapshotStoreInterface`** — the persistence port for snapshots, keyed by
  aggregate id (the "where"). It only stores and retrieves; it holds no policy.
- **`SnapshotStrategyInterface`** + **`EventCountSnapshotStrategy`** — decide when
  a snapshot is due from the current playhead (the "when"). The event-count
  strategy snapshots each time the aggregate crosses a multiple of N events.
- **`SnapshotAggregateRootLoader`** — a loader that decorates another loader:
  when a snapshot exists it restores the captured aggregate and replays only the
  events recorded after it; otherwise it delegates a full rebuild to the inner
  loader. Composed like the upcasting store: `Snapshot(EventStore(...))`. This
  saves both the read and the replay of everything up to the snapshot.
- **`Snapshotter`** — the write side: after a save, it asks the strategy whether a
  snapshot is due and, if so, captures it. Rehydration lives in the loader; this
  holds only the capture policy and the capture itself.

The aggregate is unaware of snapshotting throughout: capturing and restoring its
state is done from the outside.

## Criteria

`Shared\Criteria` is a small DSL for expressing filters and ordering in domain
terms, independent of any database:

- Composites **`AndX` / `OrX`** combine comparisons such as **`EqId`**,
  **`EqEmail`**, **`EqPlayhead`**, **`GtePlayhead`**, **`ByPlayhead`**,
  **`ByRecordedAt`**.
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
When the store is an `UpcastingEventStore`, the visited events are upcast too, so
a projector reacting to the replay sees the current event shapes.

## Specifications

`Shared\Specification\AbstractSpecification` is the base for composable domain
specifications (business rules expressed as objects).

## Exceptions

`Shared\Exception` provides a hierarchy mapping domain failures to intent —
`NotFoundException`, `ConflictException`, `ForbiddenException`,
`UnauthorizedException`, `InfrastructureException`,
`UnexpectedException` — so transport layers can translate them to the right
status without leaking domain details. Value objects validate their input with
the native `\InvalidArgumentException`. Namespace-specific exceptions
(`EventStoreException`, `CorruptSnapshotException`, `InvalidAggregateRootException`,
`InvalidExpressionException`, …) extend the base that matches their intent.

## Support

`Shared\Support\ClassName` resolves the short name of a class, used to map events
to their `applyXxx` methods.

## License
MIT. See [LICENSE](LICENSE).
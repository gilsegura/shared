# SHARED COMPONENT

[![tests](https://github.com/gilsegura/shared/actions/workflows/tests.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/tests.yaml)
[![codecov](https://codecov.io/github/gilsegura/shared/graph/badge.svg)](https://codecov.io/github/gilsegura/shared)
[![static analysis](https://github.com/gilsegura/shared/actions/workflows/static-analysis.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/static-analysis.yaml)
[![coding standards](https://github.com/gilsegura/shared/actions/workflows/coding-standards.yaml/badge.svg)](https://github.com/gilsegura/shared/actions/workflows/coding-standards.yaml)

A lightweight infrastructure component for building CQRS and Event Sourcing applications in PHP.

The component provides the building blocks for event-sourced aggregates, an event store abstraction, read-model projections, a criteria-based query layer and a semantic exception hierarchy, all framework agnostic.

## Features

* PHP 8.5+
* Event Sourcing building blocks: aggregates, entities and domain messages
* Event store abstraction with upcasting and replaying
* Read-model projections and repositories
* Criteria-based query layer with a fluent query builder
* Semantic, transport-agnostic exception hierarchy
* Strong static typing with generics
* Immutable design
* Framework agnostic

## Installation

```bash
composer require gilsegura/shared
```

## Overview

### Event-sourced aggregates

Aggregates extend `AbstractEventSourcedAggregateRoot` and rebuild their state by applying recorded domain events. Child entities extend `AbstractEventSourcedEntity`.

### Event store

`EventStoreManagerInterface` abstracts persistence of domain message streams. Streams can be upcasted through the `Upcasting` layer and traversed through the `Replaying` layer.

### Read models

Read models implement `SerializableReadModelInterface` and are populated by projectors extending `AbstractProjector`. They are retrieved through the generic `ReadModelRepositoryInterface`.

### Query layer

Queries are built on top of the `Criteria` layer. `QueryBuilder`, `SingleResultQuery` and `CollectionQuery` provide a fluent, type-safe way to compose criteria, ordering and pagination.

```php
$query = SomeQuery::of()
    ->withTenantId($tenantId)
    ->orderBy($sort)
    ->withLimit(20);
```

### Exceptions

The component exposes a semantic exception hierarchy under `Shared\Exception`. Each category describes the nature of the failure, not its transport representation:

* `NotFoundException`
* `ConflictException`
* `InvalidInputException`
* `UnauthorizedException`
* `ForbiddenException`
* `UnexpectedException`
* `InfrastructureException`

Exceptions carry no error codes or HTTP semantics. Mapping a category to a concrete protocol response is left to each application at its boundary.

## License

MIT. See [LICENSE](LICENSE).

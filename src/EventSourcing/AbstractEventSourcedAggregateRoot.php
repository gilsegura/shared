<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventInterface;
use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;

/**
 * Base aggregate root. apply() records an event and routes it to an
 * applyXxx method resolved from the event's short name;
 * uncommittedEvents() returns what to persist; initialize() rebuilds state
 * from a stream.
 */
abstract class AbstractEventSourcedAggregateRoot implements AggregateRootInterface
{
    use ResolvesApplyMethodTrait;

    /** @var DomainMessage[] */
    private array $uncommittedEvents = [];

    private int $playhead = -1;

    /**
     * Applies a stream onto the aggregate, advancing the playhead from its
     * current position. A fresh aggregate is at -1, so the full stream is
     * replayed; an aggregate restored from a snapshot already carries its
     * playhead, so only the events recorded after it are replayed. The aggregate
     * is unaware of snapshotting — it merely continues from where it is.
     *
     * @throws AggregateRootAlreadyExistsException
     */
    final public function initialize(DomainEventStream $stream): void
    {
        foreach ($stream->messages as $message) {
            ++$this->playhead;
            $this->handleRecursively($message->payload);
        }
    }

    final public function playhead(): int
    {
        return $this->playhead;
    }

    #[\Override]
    final public function uncommittedEvents(): DomainEventStream
    {
        $stream = new DomainEventStream(...$this->uncommittedEvents);

        $this->uncommittedEvents = [];

        return $stream;
    }

    /**
     * @throws AggregateRootAlreadyExistsException
     */
    final public function apply(DomainEventInterface $event): void
    {
        $this->handleRecursively($event);

        ++$this->playhead;

        $this->uncommittedEvents[] = DomainMessage::record(
            $this->id(),
            $this->playhead,
            Metadata::empty(),
            $event
        );
    }

    /**
     * @throws AggregateRootAlreadyExistsException
     */
    final protected function handleRecursively(DomainEventInterface $event): void
    {
        $this->handle($event);

        foreach ($this->childEntities() as $entity) {
            $entity->setAggregateRoot($this);
            $entity->handleRecursively($event);
        }
    }

    /**
     * @return EventSourcedEntityInterface[]
     */
    protected function childEntities(): array
    {
        return [];
    }

    private function handle(DomainEventInterface $event): void
    {
        $method = $this->applyMethod($event);

        if (method_exists($this, $method)) {
            $this->{$method}($event); // @phpstan-ignore method.dynamicName
        }
    }
}

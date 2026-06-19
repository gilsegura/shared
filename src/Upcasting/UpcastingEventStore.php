<?php

declare(strict_types=1);

namespace Shared\Upcasting;

use Shared\Criteria;
use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;
use Shared\Domain\Uuid;
use Shared\EventStore\EventStoreInterface;
use Shared\EventStore\EventStoreManagerInterface;
use Shared\EventStore\EventVisitorInterface;

/**
 * Decorates an event store so events are upcast to their current shape as they
 * leave the store — both when an aggregate is loaded and when events are visited
 * for replay. The persisted events are never modified; the upcasting happens in
 * memory on each read, driven by the chain. An empty chain is a transparent
 * pass-through.
 */
final readonly class UpcastingEventStore implements EventStoreInterface, EventStoreManagerInterface
{
    public function __construct(
        private EventStoreInterface&EventStoreManagerInterface $eventStore,
        private SequentialUpcasterChain $upcaster,
    ) {
    }

    #[\Override]
    public function load(Uuid $id, ?int $playhead = null): DomainEventStream
    {
        $stream = $this->eventStore->load($id, $playhead);
        $messages = $this->upcast($stream);

        return new DomainEventStream(...$messages);
    }

    /**
     * @return \Generator<DomainMessage>
     */
    private function upcast(DomainEventStream $stream): \Generator
    {
        foreach ($stream->messages as $message) {
            yield from ($this->upcaster)($message);
        }
    }

    #[\Override]
    public function append(DomainEventStream $stream): void
    {
        $this->eventStore->append($stream);
    }

    #[\Override]
    public function visitEvents(Criteria\AndX|Criteria\OrX $criteria, EventVisitorInterface $eventVisitor): void
    {
        // Upcast on visit too, so replaying sees the same current event shapes
        // that load() produces.
        $this->eventStore->visitEvents(
            $criteria,
            new UpcastingEventVisitor($eventVisitor, $this->upcaster),
        );
    }
}

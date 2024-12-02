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
            yield from $this->upcaster->__invoke($message);
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
        $this->eventStore->visitEvents($criteria, $eventVisitor);
    }
}

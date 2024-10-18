<?php

declare(strict_types=1);

namespace Shared\EventSourcing;

use Shared\Domain\DomainEventInterface;
use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;

abstract class AbstractEventSourcedAggregateRoot implements AggregateRootInterface
{
    /** @var DomainMessage[] */
    private array $uncommittedEvents = [];

    private int $playhead = -1;

    /**
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

        if (!method_exists($this, $method)) {
            return;
        }

        $this->$method($event);
    }

    private function applyMethod(DomainEventInterface $event): string
    {
        $fqcn = explode('\\', $event::class);

        return sprintf('apply%s', end($fqcn));
    }
}

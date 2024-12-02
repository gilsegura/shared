<?php

declare(strict_types=1);

namespace Shared\Tests\EventStore;

use Shared\Criteria;
use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;
use Shared\Domain\Uuid;
use Shared\EventStore\EventStoreInterface;
use Shared\EventStore\EventStoreManagerInterface;
use Shared\EventStore\EventVisitorInterface;
use Shared\EventStore\StreamNotFoundException;

final class InMemoryEventStore implements EventStoreInterface, EventStoreManagerInterface
{
    private array $data = [];

    #[\Override]
    public function load(Uuid $id, ?int $playhead = null): DomainEventStream
    {
        if (!isset($this->data[$id->uuid])) {
            throw StreamNotFoundException::id($id);
        }

        $messages = $this->data[$id->uuid];

        return new DomainEventStream(...$messages);
    }

    #[\Override]
    public function append(DomainEventStream $stream): void
    {
        foreach ($stream->messages as $message) {
            $this->data[$message->id->uuid][$message->playhead] = $message;
        }
    }

    #[\Override]
    public function visitEvents(Criteria\AndX|Criteria\OrX $criteria, EventVisitorInterface $eventVisitor): void
    {
        /** @var Criteria\Expr\AndX $andX */
        $andX = $criteria->expr();
        /** @var Criteria\Expr\Comparison $expr */
        $expr = $andX->expressions[0];
        /** @var Uuid $id */
        $id = $expr->value;
        /** @var DomainMessage[] $stream */
        $stream = $this->data[$id->uuid];

        foreach ($stream as $message) {
            $eventVisitor->__invoke($message);
        }
    }
}

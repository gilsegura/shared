<?php

declare(strict_types=1);

namespace Shared\Tests\Snapshotting;

use Shared\Domain\Uuid;
use Shared\EventSourcing\AbstractEventSourcedAggregateRoot;

final class SnapshottableAggregateRoot extends AbstractEventSourcedAggregateRoot
{
    private Uuid $id;

    protected function applySnapshottableAggregateRootWasTouched(SnapshottableAggregateRootWasTouched $event): void
    {
        $this->id = $event->id;
    }

    #[\Override]
    public function id(): Uuid
    {
        return $this->id;
    }
}

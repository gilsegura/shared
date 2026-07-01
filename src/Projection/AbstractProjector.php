<?php

declare(strict_types=1);

namespace Shared\Projection;

use Shared\Domain\DomainMessage;
use Shared\EventHandling\EventListenerInterface;
use Shared\EventSourcing\ResolvesApplyMethodTrait;

/**
 * Base projector: the shared mechanism behind any projection — a read model or
 * an index. It implements EventListenerInterface and resolves an applyXxx method
 * from each event's short name, ignoring events it has no method for, so a
 * concrete projector only writes handlers for the events it reacts to.
 */
abstract readonly class AbstractProjector implements EventListenerInterface
{
    use ResolvesApplyMethodTrait;

    #[\Override]
    final public function __invoke(DomainMessage $message): void
    {
        $event = $message->payload;
        $method = $this->applyMethod($event);

        if (method_exists($this, $method)) {
            $this->{$method}($event); // @phpstan-ignore method.dynamicName
        }
    }
}

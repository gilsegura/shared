<?php

declare(strict_types=1);

namespace Shared\EventHandling;

use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;

/**
 * Publishes domain messages to its listeners in order. Fail-fast: the first
 * listener that throws aborts publishing and the error is propagated wrapped in
 * an EventBusException.
 *
 * A reentrancy guard lets a listener publish further events while publishing is
 * in progress: those are appended to the queue and drained by the loop already
 * running, preserving order.
 */
final class SimpleEventBus implements EventBusInterface
{
    /** @var EventListenerInterface[] */
    private array $eventListeners;

    /** @var DomainMessage[] */
    private array $queue = [];

    private bool $isPublishing = false;

    public function __construct(
        EventListenerInterface ...$eventListeners,
    ) {
        $this->eventListeners = $eventListeners;
    }

    #[\Override]
    public function __invoke(DomainEventStream $stream): void
    {
        $this->queue = [...$this->queue, ...$stream->messages];

        if ($this->isPublishing) {
            return;
        }

        $this->isPublishing = true;

        try {
            while ([] !== $this->queue) {
                $message = array_shift($this->queue);

                foreach ($this->eventListeners as $eventListener) {
                    try {
                        $eventListener($message);
                    } catch (\Throwable $throwable) {
                        throw EventBusException::fromThrowable($throwable);
                    }
                }
            }
        } finally {
            $this->isPublishing = false;
        }
    }
}

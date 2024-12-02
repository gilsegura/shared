<?php

declare(strict_types=1);

namespace Shared\EventHandling;

use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;

final class SimpleEventBus implements EventBusInterface
{
    /** @var EventListenerInterface[] */
    private array $eventListeners = [];

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
        foreach ($stream->messages as $message) {
            $this->queue[] = $message;
        }

        if (!$this->isPublishing) {
            $this->isPublishing = true;

            try {
                while ($message = array_shift($this->queue)) {
                    foreach ($this->eventListeners as $eventListener) {
                        try {
                            $eventListener->__invoke($message);
                        } catch (\Throwable $e) {
                            throw EventBusException::throwable($e);
                        }
                    }
                }
            } finally {
                $this->isPublishing = false;
            }
        }
    }
}

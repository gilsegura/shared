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

    #[\Override]
    public function subscribe(EventListenerInterface $eventListener): void
    {
        $this->eventListeners[] = $eventListener;
    }

    #[\Override]
    public function publish(DomainEventStream $stream): void
    {
        foreach ($stream->messages as $message) {
            $this->queue[] = $message;
        }

        if (!$this->isPublishing) {
            $this->isPublishing = true;

            try {
                while ($message = array_shift($this->queue)) {
                    $this->handle($message);
                }
            } finally {
                $this->isPublishing = false;
            }
        }
    }

    private function handle(DomainMessage $message): void
    {
        foreach ($this->eventListeners as $eventListener) {
            try {
                $eventListener->handle($message);
            } catch (\Throwable $e) {
                throw EventBusException::new($e);
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace Shared\ReadModel;

use Shared\Domain\DomainEventInterface;
use Shared\Domain\DomainMessage;
use Shared\EventHandling\EventListenerInterface;

abstract readonly class AbstractProjector implements EventListenerInterface
{
    #[\Override]
    final public function __invoke(DomainMessage $message): void
    {
        $event = $message->payload;
        $method = $this->applyMethod($event);

        if (!method_exists($this, $method)) {
            return;
        }

        $this->$method($event);
    }

    private function applyMethod(DomainEventInterface $event): string
    {
        $className = explode('\\', $event::class);

        return sprintf('apply%s', $className[count($className) - 1]);
    }
}

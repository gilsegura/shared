<?php

declare(strict_types=1);

namespace Shared\ReadModel;

use Shared\Domain\DomainMessage;
use Shared\EventHandling\EventListenerInterface;
use Shared\EventSourcing\ResolvesApplyMethodTrait;

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

<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

/**
 * Dispatches a command to its handler. The application depends on this; an
 * adapter (e.g. on Messenger) provides the concrete bus.
 */
interface CommandBusInterface
{
    public function __invoke(CommandInterface $command): void;
}

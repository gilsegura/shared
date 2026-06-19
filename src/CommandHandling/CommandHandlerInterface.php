<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

/**
 * Handles a single command type. Implementations are routed to the command
 * bus by the integration layer.
 *
 * @template TCommand of CommandInterface
 */
interface CommandHandlerInterface
{
}

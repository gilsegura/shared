<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

interface CommandBusInterface
{
    public function __invoke(CommandInterface $command): void;
}

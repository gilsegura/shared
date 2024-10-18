<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

interface QueryBusInterface
{
    public function ask(QueryInterface $query): Item|Collection;
}

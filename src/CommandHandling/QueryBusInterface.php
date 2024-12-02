<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

use Serializer\SerializableInterface;

interface QueryBusInterface
{
    public function __invoke(QueryInterface $query): SerializableInterface;
}

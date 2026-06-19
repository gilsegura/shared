<?php

declare(strict_types=1);

namespace Shared\Domain;

/**
 * Marker for a domain event: something that happened, serializable so it
 * can be stored and replayed.
 */
interface DomainEventInterface
{
}

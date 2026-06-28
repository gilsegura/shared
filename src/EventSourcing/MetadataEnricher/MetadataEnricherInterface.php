<?php

declare(strict_types=1);

namespace Shared\EventSourcing\MetadataEnricher;

use Shared\Domain\Metadata;

/**
 * Adds ambient context to a message's metadata (e.g. correlation id,
 * user). Receives the metadata built so far and returns it with its own
 * keys merged in.
 */
interface MetadataEnricherInterface
{
    public function __invoke(Metadata $metadata): Metadata;
}

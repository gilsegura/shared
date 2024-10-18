<?php

declare(strict_types=1);

namespace Shared\EventSourcing\MetadataEnricher;

use Shared\Domain\Metadata;

interface MetadataEnricherInterface
{
    public function enrich(Metadata $metadata): Metadata;
}

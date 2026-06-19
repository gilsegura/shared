<?php

declare(strict_types=1);

namespace Shared\Tests\Upcasting;

use Serializer\SerializableInterface;
use Shared\Domain\DomainEventInterface;

/**
 * First version of a test event. Fixture for the upcasting tests.
 *
 * @implements SerializableInterface<array{}>
 */
final readonly class EventV1WasOccurred implements DomainEventInterface, SerializableInterface
{
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self();
    }

    #[\Override]
    public function serialize(): array
    {
        return [];
    }
}

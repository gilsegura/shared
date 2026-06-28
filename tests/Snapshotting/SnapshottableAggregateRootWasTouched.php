<?php

declare(strict_types=1);

namespace Shared\Tests\Snapshotting;

use Serializer\SerializableInterface;
use Shared\Domain\DomainEventInterface;
use Shared\Domain\Uuid;

/**
 * @implements SerializableInterface<array{id: string}>
 */
final readonly class SnapshottableAggregateRootWasTouched implements DomainEventInterface, SerializableInterface
{
    public function __construct(
        public Uuid $id,
    ) {
    }

    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self(new Uuid($attributes['id']));
    }

    #[\Override]
    public function serialize(): array
    {
        return ['id' => $this->id->uuid];
    }
}

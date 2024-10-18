<?php

declare(strict_types=1);

namespace Shared\CommandHandling;

use Shared\ReadModel\SerializableReadModelInterface;

final readonly class Item
{
    private function __construct(
        public string $id,
        public string $type,
        public array $payload,
    ) {
    }

    private static function type(SerializableReadModelInterface $serializable): string
    {
        $fqcn = explode('\\', $serializable::class);

        return end($fqcn);
    }

    public static function fromSerializable(SerializableReadModelInterface $serializable): self
    {
        return new self(
            $serializable->id()->uuid,
            self::type($serializable),
            $serializable->serialize()
        );
    }
}

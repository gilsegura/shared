<?php

declare(strict_types=1);

namespace Shared\Serializer;

interface SerializableInterface
{
    public static function deserialize(array $data): self;

    public function serialize(): array;
}

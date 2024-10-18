<?php

declare(strict_types=1);

namespace Shared\Serializer;

interface SerializerInterface
{
    /**
     * @throws \Throwable
     */
    public static function deserialize(array $serializedObject): object;

    /**
     * @throws \Throwable
     */
    public static function serialize(object $object): array;
}

<?php

declare(strict_types=1);

namespace Shared\Serializer;

use Assert\Assertion;

final readonly class Serializer implements SerializerInterface
{
    #[\Override]
    public static function deserialize(array $serializedObject): object
    {
        Assertion::keyExists($serializedObject, 'class');
        Assertion::keyExists($serializedObject, 'attributes');

        Assertion::implementsInterface($serializedObject['class'], SerializableInterface::class);

        return $serializedObject['class']::deserialize($serializedObject['attributes']);
    }

    #[\Override]
    public static function serialize(object $object): array
    {
        Assertion::isInstanceOf($object, SerializableInterface::class);

        return [
            'class' => \get_class($object),
            'attributes' => $object->serialize(),
        ];
    }
}

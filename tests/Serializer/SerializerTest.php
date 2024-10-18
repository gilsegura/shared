<?php

declare(strict_types=1);

namespace Shared\Tests\Serializer;

use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Shared\Domain\Metadata;
use Shared\Serializer\Serializer;

final class SerializerTest extends TestCase
{
    public function test_must_deserialize_array_when_not_implementing_serializable(): void
    {
        self::expectException(AssertionFailedException::class);

        Serializer::deserialize(['class' => 'stdClass', 'attributes' => ['foo' => 'bar']]);
    }

    public function test_must_deserialize_array_when_implementing_serializable(): void
    {
        $serializable = Serializer::deserialize(['class' => Metadata::class, 'attributes' => []]);

        self::assertInstanceOf(Metadata::class, $serializable);
    }

    public function test_must_serialize_array_when_not_implementing_serializable(): void
    {
        self::expectException(AssertionFailedException::class);

        Serializer::serialize(new \stdClass());
    }

    public function test_must_serialize_array_when_implementing_serializable(): void
    {
        $serializable = Serializer::serialize(Metadata::empty());

        self::assertSame([
            'class' => Metadata::class,
            'attributes' => [],
        ], $serializable);
    }
}

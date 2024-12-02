<?php

declare(strict_types=1);

namespace Shared\Tests\Domain;

use PHPUnit\Framework\TestCase;
use Shared\Domain\Metadata;

final class MetadataTest extends TestCase
{
    public function test_must_merge_two_instances(): void
    {
        $some = Metadata::empty();
        $another = $some->merge(Metadata::kv('foo', 'bar'));

        self::assertSame(['foo' => 'bar'], $another->values);
    }
}

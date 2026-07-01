<?php

declare(strict_types=1);

namespace Shared\Tests\Index;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shared\Domain\Uuid;
use Shared\Index\IndexEntryInterface;
use Shared\Index\IndexInterface;
use Shared\Index\IndexKeyInterface;

final class InMemoryIndexTest extends TestCase
{
    #[Test]
    public function it_returns_no_ids_for_an_unknown_key(): void
    {
        $index = new InMemoryIndex();

        self::assertSame([], $index->lookup(new StringKey('unknown')));
    }

    #[Test]
    public function it_resolves_a_key_to_the_ids_filed_under_it(): void
    {
        $index = new InMemoryIndex();

        $first = new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac');
        $second = new Uuid('1f1d2f3a-4b5c-6d7e-8f90-0a1b2c3d4e5f');

        $index->save(new StringEntry(new StringKey('urgent'), $first));
        $index->save(new StringEntry(new StringKey('urgent'), $second));
        $index->save(new StringEntry(new StringKey('later'), $first));

        $ids = $index->lookup(new StringKey('urgent'));

        self::assertCount(2, $ids);
        self::assertTrue($first->equals($ids[0]));
        self::assertTrue($second->equals($ids[1]));
    }

    #[Test]
    public function it_removes_an_entry(): void
    {
        $index = new InMemoryIndex();

        $id = new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac');
        $entry = new StringEntry(new StringKey('urgent'), $id);

        $index->save($entry);
        $index->remove($entry);

        self::assertSame([], $index->lookup(new StringKey('urgent')));
    }
}

final readonly class StringKey implements IndexKeyInterface
{
    public function __construct(public string $value)
    {
    }
}

/**
 * @implements IndexEntryInterface<StringKey>
 */
final readonly class StringEntry implements IndexEntryInterface
{
    public function __construct(
        private StringKey $key,
        private Uuid $id,
    ) {
    }

    #[\Override]
    public function key(): StringKey
    {
        return $this->key;
    }

    #[\Override]
    public function id(): Uuid
    {
        return $this->id;
    }
}

/**
 * @implements IndexInterface<StringKey>
 */
final class InMemoryIndex implements IndexInterface
{
    /** @var array<string, Uuid[]> */
    private array $entries = [];

    #[\Override]
    public function lookup(IndexKeyInterface $key): array
    {
        return $this->entries[$key->value] ?? [];
    }

    public function save(StringEntry $entry): void
    {
        $this->entries[$entry->key()->value][] = $entry->id();
    }

    public function remove(StringEntry $entry): void
    {
        $key = $entry->key()->value;

        $this->entries[$key] = \array_values(\array_filter(
            $this->entries[$key] ?? [],
            static fn (Uuid $id): bool => !$id->equals($entry->id()),
        ));
    }
}

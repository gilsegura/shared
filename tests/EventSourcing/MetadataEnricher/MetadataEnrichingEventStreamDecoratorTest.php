<?php

declare(strict_types=1);

namespace Shared\Tests\EventSourcing\MetadataEnricher;

use PHPUnit\Framework\TestCase;
use Shared\Domain\DomainEventInterface;
use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;
use Shared\Domain\Uuid;
use Shared\EventSourcing\MetadataEnricher\MetadataEnricherInterface;
use Shared\EventSourcing\MetadataEnricher\MetadataEnrichingEventStreamDecorator;

final class MetadataEnrichingEventStreamDecoratorTest extends TestCase
{
    public function test_must_enrich_metadata(): void
    {
        $decorator = new MetadataEnrichingEventStreamDecorator(
            new MetadataEnricher()
        );

        $stream = $decorator->decorate(new DomainEventStream(DomainMessage::record(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
            0,
            Metadata::empty(),
            new AnotherEventWasOccurred()
        )));

        $messages = $stream->messages;
        $message = $messages[0];
        $metadata = $message->metadata;

        self::assertSame(['foo' => 'bar'], $metadata->values);
    }
}

final readonly class MetadataEnricher implements MetadataEnricherInterface
{
    #[\Override]
    public function enrich(Metadata $metadata): Metadata
    {
        return $metadata->merge(Metadata::kv('foo', 'bar'));
    }
}

final readonly class AnotherEventWasOccurred implements DomainEventInterface
{
    #[\Override]
    public static function deserialize(array $data): self
    {
        return new self();
    }

    #[\Override]
    public function serialize(): array
    {
        return [];
    }
}

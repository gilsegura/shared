<?php

declare(strict_types=1);

namespace Shared\EventSourcing\MetadataEnricher;

use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;
use Shared\EventSourcing\EventStreamDecoratorInterface;

/**
 * Stream decorator that runs its enrichers over every message, merging the
 * resulting metadata onto each one while preserving what was already
 * there.
 */
final readonly class MetadataEnrichingEventStreamDecorator implements EventStreamDecoratorInterface
{
    /** @var MetadataEnricherInterface[] */
    private array $enrichers;

    public function __construct(
        MetadataEnricherInterface ...$enrichers,
    ) {
        $this->enrichers = $enrichers;
    }

    #[\Override]
    public function __invoke(DomainEventStream $stream): DomainEventStream
    {
        return new DomainEventStream(...$this->enrich($stream));
    }

    /**
     * @return iterable<DomainMessage>
     */
    private function enrich(DomainEventStream $stream): iterable
    {
        foreach ($stream->messages as $message) {
            $metadata = array_reduce(
                $this->enrichers,
                static fn (Metadata $carry, MetadataEnricherInterface $enricher): Metadata => $enricher($carry),
                Metadata::empty(),
            );

            yield $message->addMetadata($metadata);
        }
    }
}

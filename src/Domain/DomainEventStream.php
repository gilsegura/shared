<?php

declare(strict_types=1);

namespace Shared\Domain;

/**
 * An ordered, iterable stream of domain messages.
 */
final readonly class DomainEventStream
{
    /** @var DomainMessage[] */
    public array $messages;

    public function __construct(
        DomainMessage ...$messages,
    ) {
        $this->messages = $messages;
    }
}

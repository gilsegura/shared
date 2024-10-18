<?php

declare(strict_types=1);

namespace Shared\Domain;

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

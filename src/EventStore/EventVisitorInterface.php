<?php

declare(strict_types=1);

namespace Shared\EventStore;

use Shared\Domain\DomainMessage;

interface EventVisitorInterface
{
    public function doWithEvent(DomainMessage $message): void;
}

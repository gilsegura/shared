<?php

declare(strict_types=1);

namespace Shared\ReadModel;

use Shared\Domain\IdentifiableInterface;

/**
 * A read model: an identifiable projection of events for querying.
 */
interface ReadModelInterface extends IdentifiableInterface
{
}

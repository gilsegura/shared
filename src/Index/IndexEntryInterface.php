<?php

declare(strict_types=1);

namespace Shared\Index;

use Shared\Domain\Uuid;

/**
 * One entry of an index: the key it is filed under and the id it points to. A
 * projector builds these from events and hands them to the index to save or
 * remove. Covariant in its key, since the key is only ever read from an entry.
 *
 * @template-covariant TKey of IndexKeyInterface
 */
interface IndexEntryInterface
{
    /**
     * @return TKey
     */
    public function key(): IndexKeyInterface;

    public function id(): Uuid;
}

<?php

declare(strict_types=1);

namespace Shared\Index;

use Shared\Domain\Uuid;

/**
 * A search/lookup index: a projection that resolves a complete key to the ids
 * filed under it. Unlike a read model it is never read or presented as a
 * resource — it only answers "which ids match this key?", so the read side can
 * then load those read models or aggregates. Kept in sync by a projector.
 *
 * This base declares only the read side (lookup), so it stays usable
 * polymorphically. Maintenance — saving and removing entries — puts the entry in
 * a parameter position, which is contravariant, so each concrete index declares
 * save()/remove() with its own native entry type instead of inheriting them
 * here. This mirrors ReadModelRepositoryInterface, where find is shared and
 * write is declared per concrete repository.
 *
 * @template TKey of IndexKeyInterface
 */
interface IndexInterface
{
    /**
     * The ids filed under the given complete key.
     *
     * @param TKey $key
     *
     * @return Uuid[]
     *
     * @throws IndexException
     */
    public function lookup(IndexKeyInterface $key): array;
}

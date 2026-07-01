<?php

declare(strict_types=1);

namespace Shared\ReadModel;

use Shared\Criteria\AndX;
use Shared\Criteria\OrderX;
use Shared\Criteria\OrX;
use Shared\Query\Pagination;

/**
 * The one thing every read model repository can do: find by criteria. The base
 * is read-only so it stays covariant in TReadModel — a Repository<Thing> is a
 * Repository<ReadModelInterface>. Writing (save, remove) makes TReadModel appear
 * in a parameter, which is contravariant, so each concrete repository declares
 * it with its own native type instead of inheriting it here.
 *
 * @template-covariant TReadModel of ReadModelInterface
 */
interface ReadModelRepositoryInterface
{
    /**
     * @return TReadModel[]
     *
     * @throws ReadModelException
     */
    public function findBy(
        AndX|OrX|null $criteria = null,
        ?OrderX $sort = null,
        ?Pagination $pagination = null,
    ): array;
}

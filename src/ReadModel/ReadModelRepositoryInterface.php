<?php

declare(strict_types=1);

namespace Shared\ReadModel;

use Shared\Criteria\AndX;
use Shared\Criteria\OrderX;
use Shared\Criteria\OrX;
use Shared\Domain\Uuid;

/**
 * @template TReadModel of SerializableReadModelInterface
 */
interface ReadModelRepositoryInterface
{
    /**
     * @return TReadModel
     *
     * @throws ReadModelRepositoryException
     */
    public function oneOrException(Uuid $id): SerializableReadModelInterface;

    /**
     * @return TReadModel[]
     *
     * @throws ReadModelRepositoryException
     */
    public function findBy(
        AndX|OrX|null $criteria = null,
        ?OrderX $sort = null,
        ?int $offset = null,
        ?int $limit = null,
    ): array;

    /**
     * @param TReadModel $readModel
     *
     * @throws ReadModelRepositoryException
     */
    public function save(SerializableReadModelInterface $readModel): void;
}

<?php

declare(strict_types=1);

namespace Shared\Criteria;

final readonly class OrderX extends AbstractCriteria
{
    public function __construct(
        CriteriaInterface ...$criterias,
    ) {
        parent::__construct(new Expr\OrderX(...array_map(
            static function (CriteriaInterface $criteria): ExpressionInterface {
                return $criteria->expr();
            }, $criterias
        )));
    }
}

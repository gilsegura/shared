<?php

declare(strict_types=1);

namespace Shared\Criteria;

final readonly class AndX extends AbstractCriteria
{
    public function __construct(
        CriteriaInterface ...$criterias,
    ) {
        parent::__construct(new Expr\AndX(...array_map(
            static function (CriteriaInterface $criteria): ExpressionInterface {
                return $criteria->expr();
            }, $criterias
        )));
    }
}

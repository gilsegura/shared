<?php

declare(strict_types=1);

namespace Shared\Criteria;

final readonly class OrX extends AbstractCriteria
{
    public function __construct(
        CriteriaInterface ...$criterias,
    ) {
        parent::__construct(new Expr\OrX(...array_map(
            static function (CriteriaInterface $criteria): ExpressionInterface {
                return $criteria->expr();
            }, $criterias
        )));
    }
}

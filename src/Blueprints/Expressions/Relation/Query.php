<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Expressions\Relation;

use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Expressions\BlueprintExpression;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Aggregate\BlueprintAggregate;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Aggregate\ColumnAgg;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;

class Query extends BlueprintExpression
{
    protected $relation;

    protected BlueprintAggregate $select;

    public function __construct(EloquentRelation $relation)
    {
        $this->relation = $relation;
    }

    public function resolve(array $attributes)
    {
        return null;
    }

    public function select($value)
    {
        if (!$value instanceof BlueprintAggregate) {
            /** @var ColumnAgg $value */
            $value = $this->make(new ColumnAgg($value));
        }

        $this->select = $value;
        $this->relation->select($value->toExpression());

        $value->addConstraints($this->relation);

        return $this;
    }

    public function toSql()
    {
        return $this->relation->toSql();
    }

    public function getRelation(): EloquentRelation
    {
        return $this->relation;
    }

    public function getSelect(): BlueprintAggregate
    {
        return $this->select;
    }
}

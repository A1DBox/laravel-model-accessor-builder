<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Relation;

use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Blueprint;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;

class Query extends Blueprint
{
    protected $select;

    protected $relation;

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
        $this->select = $this->valueOrExpression($value);

        return $this;
    }

    public function toSql()
    {
        $this->relation->select($this->select);

        return $this->relation->toSql();
    }

    /**
     * @return EloquentRelation
     */
    public function getRelation(): EloquentRelation
    {
        return $this->relation;
    }
}

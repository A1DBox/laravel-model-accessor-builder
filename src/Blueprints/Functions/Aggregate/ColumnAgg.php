<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Aggregate;

use Illuminate\Database\Eloquent\Relations\Relation;

class ColumnAgg extends JsonAgg
{
    public function resolveAggregate()
    {
        return $this->aggregateItems;
    }

    public function test()
    {

    }

    public function addConstraints(Relation $relation)
    {
        $relation->withCasts([
            $this->getColumnName() => 'array'
        ]);
    }
}

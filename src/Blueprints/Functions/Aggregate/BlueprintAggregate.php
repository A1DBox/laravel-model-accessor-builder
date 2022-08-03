<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Aggregate;

use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Blueprint;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class BlueprintAggregate extends Blueprint
{
    protected array $aggregateItems = [];

    protected function addAggregateItem($value)
    {
        return $this->aggregateItems[] = $value;
    }

    abstract public function resolveAggregate();

    public function addConstraints(Relation $relation)
    {

    }
}

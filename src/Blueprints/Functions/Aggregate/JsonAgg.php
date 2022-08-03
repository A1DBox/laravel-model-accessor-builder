<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Aggregate;

use A1DBox\Laravel\ModelAccessorBuilder\Contracts\BlueprintHasColumnName;

class JsonAgg extends BlueprintAggregate implements BlueprintHasColumnName
{
    protected $column;

    public function __construct($column)
    {
        $this->column = $column;
    }

    public function resolve(array $attributes)
    {
        if (!$this->tryResolveValue($this->column, $attributes, $value)) {
            $value = $attributes[$this->column] ?? null;
        }

        return $this->addAggregateItem($value);
    }

    public function resolveAggregate()
    {
        return json_encode($this->aggregateItems, JSON_UNESCAPED_UNICODE);
    }

    public function toSql()
    {
        return $this->grammar->compileFunctionCall('json_agg', [
            $this->grammar->compileColumnName($this->column)
        ]);
    }

    public function getColumnName(): string
    {
        if ($this->column instanceof BlueprintHasColumnName) {
            return $this->column->getColumnName();
        }

        return $this->column;
    }
}

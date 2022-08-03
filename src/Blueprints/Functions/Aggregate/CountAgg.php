<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Aggregate;

class CountAgg extends BlueprintAggregate
{
    protected $expression;

    protected $count;

    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    public function resolve(array $attributes)
    {
        if ($this->expression === '*') {
            return $this->addAggregateItem(true);
        }

        if (!$this->tryResolveValue($this->expression, $attributes, $value)) {
            $value = $attributes[$this->expression] ?? null;
        }

        return $this->addAggregateItem($value);
    }

    public function resolveAggregate()
    {
        return count(array_filter(
            $this->aggregateItems,
            static fn($item) => $item !== null
        ));
    }

    public function toSql()
    {
        return $this->grammar->compileCount($this->expression);
    }
}

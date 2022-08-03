<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions;

use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Blueprint;

class Coalesce extends Blueprint
{
    protected array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function resolve(array $attributes)
    {
        foreach ($this->resolveValue($this->values, $attributes) as $value) {
            if ($value === null) {
                continue;
            }

            return $value;
        }

        return null;
    }

    public function toSql()
    {
        return $this->grammar->compileCoalesce($this->values);
    }
}

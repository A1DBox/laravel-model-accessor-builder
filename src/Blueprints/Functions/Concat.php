<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions;

use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Blueprint;

class Concat extends Blueprint
{
    protected array $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    public function resolve(array $attributes)
    {
        return implode('', $this->resolveValue($this->columns, $attributes));
    }

    public function toSql()
    {
        return $this->grammar->compileConcat($this->columns);
    }
}

<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions;

use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Column;
use Illuminate\Support\Arr;

class JsonAgg extends Column
{
    public function resolve(array $attributes)
    {
        $value = Arr::wrap($attributes[$this->column] ?? null);

        dd('resolve json_agg');

        return $value;
    }

    public function toSql()
    {
        return $this->grammar->compileFunctionCall('json_agg', [
            $this->grammar->compileColumnName($this->column, $this->table)
        ]);
    }
}

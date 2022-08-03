<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints;

use A1DBox\Laravel\ModelAccessorBuilder\Contracts\BlueprintHasColumnName;

class Column extends Blueprint implements BlueprintHasColumnName
{
    protected $table;

    protected $column;

    public function __construct(string $column, $table)
    {
        $this->column = $column;
        $this->table = $table;
    }

    public function resolve(array $attributes)
    {
        return $attributes[$this->column] ?? null;
    }

    public function toSql()
    {
        return $this->grammar->compileColumnName($this->column, $this->table);
    }

    public function getColumnName(): string
    {
        return $this->column;
    }
}

<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints;

class Column extends Blueprint
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
}

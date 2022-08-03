<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Blueprint;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Column;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Aggregate\CountAgg;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Aggregate\JsonAgg;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Coalesce;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Concat;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Trim;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Relation;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Text;

class BlueprintCabinet
{
    protected AccessorBuilder $accessorBuilder;

    public function __construct(AccessorBuilder $accessorBuilder)
    {
        $this->accessorBuilder = $accessorBuilder;
    }

    public function str($value)
    {
        return $this->prepare(new Text($value));
    }

    public function col($column, $table = null)
    {
        return $this->prepare(new Column($column, $table));
    }

    public function trim($item)
    {
        return $this->prepare(new Trim($item));
    }

    public function concat(...$columns)
    {
        return $this->prepare(new Concat($columns));
    }

    public function coalesce(...$columns)
    {
        return $this->prepare(new Coalesce($columns));
    }

    public function jsonAgg($column)
    {
        return $this->prepare(new JsonAgg($column));
    }

    public function countAgg($column = '*')
    {
        return $this->prepare(new CountAgg($column));
    }

    public function relation($name, callable $callback)
    {
        return $this->prepare(new Relation($name, $callback));
    }

    protected function prepare(Blueprint $blueprint): Blueprint
    {
        return $blueprint->prepare($this->accessorBuilder);
    }
}

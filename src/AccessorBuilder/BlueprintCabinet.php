<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Blueprint;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Column;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Concat;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions\Trim;
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

    protected function prepare(Blueprint $blueprint): Blueprint
    {
        return $blueprint->prepare($this->accessorBuilder);
    }
}

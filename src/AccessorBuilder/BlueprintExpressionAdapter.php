<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;

use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Blueprint;
use Illuminate\Database\Query\Expression;

class BlueprintExpressionAdapter extends Expression
{
    protected Blueprint $blueprint;

    public function __construct(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;

        parent::__construct(null);
    }

    public function getValue()
    {
        return $this->blueprint->toSql();
    }
}

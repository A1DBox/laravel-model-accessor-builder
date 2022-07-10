<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Functions;

use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Blueprint;

class Trim extends Blueprint
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function resolve(array $attributes)
    {
        return trim($this->resolveValue($this->value, $attributes));
    }

    public function toSql()
    {
        return $this->grammar->compileTrim($this->value);
    }
}

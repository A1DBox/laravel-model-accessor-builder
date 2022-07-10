<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints;

class Text extends Blueprint
{
    protected $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function resolve(array $attributes)
    {
        return $this->value;
    }

    public function toSql()
    {
        return $this->grammar->compileString($this->value);
    }
}

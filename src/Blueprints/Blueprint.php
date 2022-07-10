<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints;

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\Contracts\ExpressionGrammar;
use Illuminate\Database\Connection;
use Illuminate\Support\Arr;

abstract class Blueprint
{
    protected AccessorBuilder $accessorBuilder;

    protected ExpressionGrammar $grammar;

    protected Connection $connection;

    /**
     * @param array $attributes All attributes from model
     * @return string
     */
    abstract public function resolve(array $attributes);

    abstract public function toSql();

    public function prepare(AccessorBuilder $accessorBuilder)
    {
        $this->accessorBuilder = $accessorBuilder;
        $this->grammar = $accessorBuilder->getGrammar();
        $this->connection = $accessorBuilder->getConnection();

        return $this;
    }

    protected function resolveValue($value, array $attributes)
    {
        $isArray = is_array($value);
        $result = [];

        foreach (Arr::wrap($value) as $item) {
            if ($item instanceof self) {
                $result[] = $item->resolve($attributes);
            } else {
                $result[] = $item;
            }
        }

        return $isArray ? $result : Arr::first($result);
    }

    public function __toString()
    {
        return (string)$this->toSql();
    }
}

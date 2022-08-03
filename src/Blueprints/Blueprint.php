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

    public static function create()
    {
        return new static;
    }

    /**
     * @param array $attributes All attributes from model
     * @return string
     */
    abstract public function resolve(array $attributes);

    abstract public function toSql();

    protected function tryResolveValue($blueprint, array $attributes, &$value)
    {
        if ($blueprint instanceof self) {
            $value = $blueprint->resolve($attributes);
            return true;
        }

        return false;
    }

    /**
     * @param AccessorBuilder|self $from
     * @return $this
     */
    public function prepare($from)
    {
        if ($from instanceof self) {
            $from = $from->accessorBuilder;
        }

        $this->accessorBuilder = $from;
        $this->grammar = $from->getGrammar();
        $this->connection = $from->getConnection();

        return $this;
    }

    /**
     * @param Blueprint $blueprint
     * @return static
     */
    protected function make(self $blueprint)
    {
        return $blueprint->prepare($this);
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

    protected function toExpression()
    {
        return new AccessorBuilder\BlueprintExpressionAdapter($this);
    }

    public function __toString()
    {
        return (string)$this->toSql();
    }
}

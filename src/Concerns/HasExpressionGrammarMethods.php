<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Concerns;

use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Column;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;

trait HasExpressionGrammarMethods
{
    protected Connection $connection;

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function compileTrim($value)
    {
        return $this->compileFunctionCall('trim', [$value]);
    }

    public function compileConcat(array $columns)
    {
        return $this->compileFunctionCall('concat', $columns);
    }

    public function compileCoalesce(array $values)
    {
        return $this->compileFunctionCall('coalesce', $values);
    }

    public function compileCount($expression)
    {
        return $this->compileFunctionCall('count', [$expression]);
    }

    public function compileString(string $value)
    {
        return $this->connection->getPdo()->quote($value);
    }

    public function compileFunctionCall($name, array $arguments)
    {
        return sprintf('%s(%s)', $name, $this->joinArguments($arguments));
    }

    public function compileColumnName($column, $table = null)
    {
        if ($column instanceof Column) {
            return $column->toSql();
        }

        $column = (string)$column;

        $result = '';

        if ($table) {
            if ($table instanceof Model) {
                $table = $table->getTable();
            }

            if (class_exists($table)) {
                $table = (new $table)->getTable();
            }

            $result .= $table . '.';
        }

        $result .= $column;

        return $this->wrap($result);
    }

    protected function joinArguments(array $items)
    {
        return implode(', ', $items);
    }
}

<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Concerns;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;

trait HasExpressionGrammarMethods
{
    protected Connection $connection;

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function compileString(string $value)
    {
        return $this->connection->getPdo()->quote($value);
    }

    public function compileColumnName(string $column, $table)
    {
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

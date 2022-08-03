<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Contracts;

use Illuminate\Database\Connection;
use Illuminate\Database\Grammar;

/**
 * @mixin Grammar
 */
interface ExpressionGrammar
{
    public function compileTrim($value);

    public function compileConcat(array $columns);

    public function compileCoalesce(array $values);

    public function compileCount($expression);

    public function compileString(string $value);

    public function compileColumnName($column, $table = null);

    public function setConnection(Connection $connection);
}

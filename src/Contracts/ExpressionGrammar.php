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

    public function compileString(string $value);

    public function compileColumnName(string $column, $table);

    public function setConnection(Connection $connection);
}

<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Query\Grammar;

use A1DBox\Laravel\ModelAccessorBuilder\Concerns\HasExpressionGrammarMethods;
use A1DBox\Laravel\ModelAccessorBuilder\Contracts\ExpressionGrammar;
use Illuminate\Database\Query\Grammars\SQLiteGrammar as Base;

class SQLiteGrammar extends Base implements ExpressionGrammar
{
    use HasExpressionGrammarMethods;

    public function compileTrim($value)
    {
        return 'trim(' . $value . ')';
    }

    public function compileConcat(array $columns)
    {
        return 'concat(' . $this->joinArguments($columns) . ')';
    }
}

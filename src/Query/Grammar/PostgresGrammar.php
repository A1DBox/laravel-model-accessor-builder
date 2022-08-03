<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Query\Grammar;

use A1DBox\Laravel\ModelAccessorBuilder\Concerns\HasExpressionGrammarMethods;
use A1DBox\Laravel\ModelAccessorBuilder\Contracts\ExpressionGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar as Base;

class PostgresGrammar extends Base implements ExpressionGrammar
{
    use HasExpressionGrammarMethods;
}

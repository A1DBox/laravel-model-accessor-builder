<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Tests\Models;

use A1DBox\Laravel\ModelAccessorBuilder\Concerns\HasAccessorBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Base;

/**
 * Used only for tests
 *
 * @method Builder|Model newQuery()
 * @method Builder|Model query()
 * @method Builder withAccessor($accessors)
 */
abstract class Model extends Base
{
    use HasAccessorBuilder;
}

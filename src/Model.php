<?php

namespace A1DBox\Laravel\ModelAccessorBuilder;

use A1DBox\Laravel\ModelAccessorBuilder\Concerns\HasAccessorBuilder;
use Illuminate\Database\Eloquent\Model as Base;

abstract class Model extends Base
{
    use HasAccessorBuilder;
}

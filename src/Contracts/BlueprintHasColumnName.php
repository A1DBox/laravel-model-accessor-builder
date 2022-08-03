<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Contracts;

interface BlueprintHasColumnName
{
    public function getColumnName(): string;
}

<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Model;

$model = new class extends Model {
    protected $attributes = [
        'name' => 'John',
        'last_name' => 'Doe',
    ];

    public function getConcatAttribute($value = null)
    {
        return AccessorBuilder::make(
            $this,
            $value,
            fn(BlueprintCabinet $cabinet) => $cabinet
                ->concat(
                    $cabinet->col('name'),
                    $cabinet->str(' '),
                    $cabinet->col('last_name'),
                )
        );
    }
};

$accessor = $model->getConcatAttribute();

it('generates concat SQL', function () use ($accessor) {
    expect($accessor->toSql())
        ->toBe(<<<STR
concat("name", ' ', "last_name")
STR);
});

it('does concat from attributes', function () use ($accessor) {
    expect($accessor->resolveModelValue())
        ->toBe('John Doe');
});

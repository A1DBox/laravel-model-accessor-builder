<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Model;

$model = new class extends Model {
    protected $attributes = [
        'to_trim' => 'John  ',
    ];

    public function getTrimAttribute($value = null)
    {
        return AccessorBuilder::make(
            $this,
            $value,
            fn(BlueprintCabinet $cabinet) => $cabinet
                ->trim(
                    $cabinet->col('to_trim'),
                )
        );
    }
};

$accessor = $model->getTrimAttribute();

it('generates trim SQL', function () use ($accessor) {
    expect($accessor->toSql())
        ->toBe(<<<STR
trim("to_trim")
STR);
});

it('does trim on attribute', function () use ($accessor) {
    expect($accessor->resolveModelValue())
        ->toBe('John');
});

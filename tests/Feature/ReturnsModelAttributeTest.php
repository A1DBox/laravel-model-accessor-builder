<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Model;

$model = new class extends Model {
    protected $attributes = [
        'full_name' => 'John Doe',
    ];

    public function getFullNameAttribute($value = null)
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

//When attribute value generated during SQL query
it('returns accessor original value from model attributes', function () use ($model) {
    expect($model->getAttribute('full_name'))
        ->toBe('John Doe');
});

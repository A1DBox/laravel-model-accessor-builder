<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Model;

$model = new class extends Model {
    protected $guarded = [];

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

    public function getFullNameTrimAttribute($value = null)
    {
        return AccessorBuilder::make(
            $this,
            $value,
            fn(BlueprintCabinet $cabinet) => $cabinet->trim(
                $cabinet->concat(
                    $cabinet->col('last_name'),
                    $cabinet->str(' '),
                    $cabinet->col('name'),
                    $cabinet->str(' '),
                    $cabinet->col('middle_name'),
                )
            )
        );
    }
};

//When attribute value generated during SQL query
it('returns accessor original value from model attributes', function () use ($model) {
    $model->fill(['full_name' => 'John Doe']);
    expect($model->getAttribute('full_name'))
        ->toBe('John Doe');

    $model->fill(['full_name' => '']);
    expect($model->getAttribute('full_name'))
        ->toBe('');

    //If full name, and no attributes in model, just " " symbol will concat
    $model->fill(['full_name' => null]);
    expect($model->getAttribute('full_name'))
        ->toBe(' ');

    expect($model->getAttribute('full_name_trim'))
        ->toBe('');
});

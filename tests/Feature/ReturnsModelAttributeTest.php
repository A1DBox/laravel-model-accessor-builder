<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Model;

$model = new class extends Model {
    protected $guarded = [];

    public function getFullNameAttribute()
    {
        return AccessorBuilder::make(
            $this,
            fn(BlueprintCabinet $cabinet) => $cabinet
                ->concat(
                    $cabinet->col('name'),
                    $cabinet->str(' '),
                    $cabinet->col('last_name'),
                )
        );
    }

    public function getFullNameTrimAttribute()
    {
        return AccessorBuilder::make(
            $this,
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

it('returns accessor original value from model attributes when it exists', function () use ($model) {
    //If full_name attribute already exists in model attributes, it must return value from model attributes
    $model->fill(['full_name' => null]);
    expect($model->getAttribute('full_name'))
        ->toBeNull();

    $model->fill(['full_name' => 'John Doe']);
    expect($model->getAttribute('full_name'))
        ->toBe('John Doe');

    $model->fill(['full_name' => '']);
    expect($model->getAttribute('full_name'))
        ->toBe('');
});

it('generates accessor value in realtime if attribute not exists in model', function () use ($model) {
    $model->setRawAttributes([]);

    //If full_name_trim attribute in model not exists, the value will be realtime-resolved with accessor builder
    //must return empty string because logic of trim(concat(null, ' ', null)) used
    expect($model->getAttribute('full_name_trim'))
        ->toBe('');

    //If full_name attribute in model not exists, the value will be realtime-resolved with accessor builder
    //must return space symbol " ", because logic of concat(null, ' ', null)
    expect($model->getAttribute('full_name'))
        ->toBe(' ');

});

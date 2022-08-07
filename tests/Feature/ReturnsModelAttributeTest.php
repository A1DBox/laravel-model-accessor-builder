<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Tests\Feature;

use A1DBox\Laravel\ModelAccessorBuilder\Tests\Models\User;

it('returns accessor original value from model attributes when it exists', function () {
    $model = User::make();

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

it('generates accessor value in realtime if attribute not exists in model', function () {
    $model = User::make();

    //If full_name_trim attribute in model not exists, the value will be realtime-resolved with accessor builder
    //must return empty string because logic of trim(concat(null, ' ', null)) used, and model not contains any attribute
    expect($model->getAttribute('full_name_trim'))
        ->toBe('');

    //If full_name attribute in model not exists, the value will be realtime-resolved with accessor builder
    //must return space symbol "  ", because logic of concat(null, ' ', null, ' ', null)
    expect($model->getAttribute('full_name'))
        ->toBe('  ');
});

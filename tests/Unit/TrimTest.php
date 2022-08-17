<?php

/** @noinspection SqlNoDataSourceInspection SqlResolve */

namespace A1DBox\Laravel\ModelAccessorBuilder\Tests\Unit;

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Tests\Models\Model;

$model = new class extends Model {
    protected $table = 'users';

    protected $attributes = [
        'to_trim' => 'John  ',
    ];

    public function getTrimAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet
                ->trim(
                    $cabinet->col('to_trim'),
                )
        );
    }
};

$accessor = $model->getAttributeAccessorBuilder('trim');

it('generates trim() SQL', function () use ($accessor) {
    $sql = <<<CALL
trim("to_trim")
CALL;

    expect($accessor->toSql())
        ->toBe($sql);
});

it('appends trim() SQL to query', function () use ($model) {
    $sql = <<<SQL
select *, (trim("to_trim")) AS "trim" from "users"
SQL;

    expect($sql)->toBe($model->newQuery()
        ->withAccessor('trim')
        ->toSql()
    );
});

it('does trim() on attribute', function () use ($accessor) {
    expect($accessor->resolveModelValue())
        ->toBe('John');
});

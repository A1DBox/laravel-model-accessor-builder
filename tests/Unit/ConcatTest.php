<?php

/** @noinspection SqlNoDataSourceInspection SqlResolve */

namespace A1DBox\Laravel\ModelAccessorBuilder\Tests\Unit;

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Tests\Models\Model;

$model = new class extends Model {
    protected $table = 'users';

    protected $attributes = [
        'name' => 'John',
        'last_name' => 'Doe',
    ];

    public function getConcatAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet
                ->concat(
                    $cabinet->col('name'),
                    $cabinet->str(' '),
                    $cabinet->col('last_name'),
                )
        );
    }
};

$accessor = $model->getAttributeAccessorBuilder('concat');

it('generates concat() SQL', function () use ($accessor) {
    $sql = <<<CALL
concat("name", ' ', "last_name")
CALL;

    expect($accessor->toSql())
        ->toBe($sql);
});

it('appends concat() SQL to query', function () use ($model) {
    $sql = <<<SQL
select *, (concat("name", ' ', "last_name")) AS "concat" from "users"
SQL;

    expect($sql)->toBe($model->newQuery()
        ->withAccessor('concat')
        ->toSql()
    );
});

it('does concat() from attributes', function () use ($accessor) {
    expect($accessor->resolveModelValue())
        ->toBe('John Doe');
});

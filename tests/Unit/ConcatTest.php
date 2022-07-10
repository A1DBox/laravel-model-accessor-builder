<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Model;
use Illuminate\Support\Str;

$model = new class extends Model {
    protected $attributes = [
        'name' => 'John',
        'last_name' => 'Doe',
    ];

    public function getConcatAttribute()
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
};

$accessor = $model->getConcatAttribute();

it('generates concat SQL', function () use ($accessor) {
    expect($accessor->toSql())
        ->toBe(<<<STR
concat("name", ' ', "last_name")
STR);
});

it('appends concat SQL to query', function () use ($model) {
    $except = <<<SQL
select *, (concat("name", ' ', "last_name")) AS "concat" from "concat_test"."*"
SQL;

    $sql = $model->newQuery()
        ->withAccessor('concat')
        ->toSql();

    expect(Str::is($except, $sql))->toBeTrue();
});

it('does concat from attributes', function () use ($accessor) {
    expect($accessor->resolveModelValue())
        ->toBe('John Doe');
});

<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Model;
use Illuminate\Support\Str;

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

it('appends trim SQL to query', function () use ($model) {
    $except = <<<SQL
select *, (trim("to_trim")) AS "trim" from "trim_test"."*"
SQL;

    $sql = $model->newQuery()
        ->withAccessor('trim')
        ->toSql();

    expect(Str::is($except, $sql))->toBeTrue();
});

it('does trim on attribute', function () use ($accessor) {
    expect($accessor->resolveModelValue())
        ->toBe('John');
});

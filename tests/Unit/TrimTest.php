<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Model;
use Illuminate\Support\Str;

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
    expect($accessor->toSql())
        ->toBe(<<<STR
trim("to_trim")
STR);
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

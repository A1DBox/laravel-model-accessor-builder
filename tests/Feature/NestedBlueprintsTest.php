<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Model;
use Illuminate\Support\Str;

$model = new class extends Model {
    protected $table = 'test';

    protected $guarded = [];

    public function getFullNameAttribute()
    {
        return AccessorBuilder::make(
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

    public function getFullNameQualifiedAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet->trim(
                $cabinet->concat(
                    $cabinet->col('last_name', $this),
                    $cabinet->str(' '),
                    $cabinet->col('name', 'test'),
                    $cabinet->str(' '),
                    $cabinet->col('middle_name', $this),
                )
            )
        );
    }
};

it('generates trim(concat()) SQL', function () use ($model) {
    $sql = <<<SQL
select *, (trim(concat("last_name", ' ', "name", ' ', "middle_name"))) AS "full_name" from "test"
SQL;

    expect($sql)->toBe($model->newQuery()
        ->withAccessor('full_name')
        ->toSql()
    );
});

it('generates trim(concat()) SQL with qualified table', function () use ($model) {
    $sql = <<<SQL
select *, (trim(concat("test"."last_name", ' ', "test"."name", ' ', "test"."middle_name"))) AS "full_name_qualified" from "test"
SQL;

    expect($sql)->toBe($model->newQuery()
        ->withAccessor('full_name_qualified')
        ->toSql()
    );
});

it('does trim(concat()) on attributes', function () use ($model) {
    $model->fill([
        'last_name' => 'John',
        'name' => 'Doe',
        'middle_name' => '',
    ]);

    expect($model->getAttribute('full_name'))
        ->toBe('John Doe');
});

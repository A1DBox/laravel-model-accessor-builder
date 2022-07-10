<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Model;
use Illuminate\Support\Str;

$model = new class extends Model {
    protected $guarded = [];

    public function getFullNameAttribute($value = null)
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

it('generates trim(concat()) SQL', function () use ($model) {
    $except = <<<SQL
select *, (trim(concat("last_name", ' ', "name", ' ', "middle_name"))) AS "full_name" from "nested_blueprints_test"."*"
SQL;

    $sql = $model->newQuery()
        ->withAccessor('full_name')
        ->toSql();

    expect(Str::is($except, $sql))->toBeTrue();
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

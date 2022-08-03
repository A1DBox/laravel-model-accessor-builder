<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Model;

$model = new class extends Model {
    protected $table = 'users';

    protected $attributes = [
        'name' => 'John',
        'last_name' => null,
    ];

    public function getFullNameAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet
                ->concat(
                    $cabinet->col('name'),
                    $cabinet->str(' '),
                    $cabinet->coalesce(
                        $cabinet->col('last_name'),
                        $cabinet->str('<empty>')
                    ),
                )
        );
    }
};

$accessor = $model->getAttributeAccessorBuilder('full_name');

it('generates coalesce SQL', function () use ($accessor) {
    expect($accessor->toSql())
        ->toBe(<<<STR
concat("name", ' ', coalesce("last_name", '<empty>'))
STR);
});

it('appends coalesce SQL to query', function () use ($model) {
    $sql = <<<SQL
select *, (concat("name", ' ', coalesce("last_name", '<empty>'))) AS "full_name" from "users"
SQL;

    expect($sql)->toBe($model->newQuery()
        ->withAccessor('full_name')
        ->toSql()
    );
});

it('does coalesce from attributes', function () use ($accessor) {
    expect($accessor->resolveModelValue())
        ->toBe('John <empty>');
});

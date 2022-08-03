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

    public function getNameAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet
                ->coalesce(
                    $cabinet->col('name'),
                    $cabinet->str('<empty>')
                )
        );
    }

    public function getLastNameAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet
                ->coalesce(
                    $cabinet->col('last_name'),
                    $cabinet->str('<empty>')
                )
        );
    }
};

$accessorName = $model->getAttributeAccessorBuilder('name');
$accessorLastName = $model->getAttributeAccessorBuilder('last_name');

it('generates coalesce() SQL', function () use ($accessorName) {
    expect($accessorName->toSql())
        ->toBe(<<<STR
coalesce("name", '<empty>')
STR);
});

it('appends coalesce() SQL to query', function () use ($model) {
    $sql = <<<SQL
select *, (coalesce("name", '<empty>')) AS "name" from "users"
SQL;

    expect($sql)->toBe($model->newQuery()
        ->withAccessor('name')
        ->toSql()
    );
});

it('does coalesce() from attributes', function () use ($accessorName, $accessorLastName) {
    expect($accessorName->resolveModelValue())
        ->toBe('John');

    expect($accessorLastName->resolveModelValue())
        ->toBe('<empty>');
});

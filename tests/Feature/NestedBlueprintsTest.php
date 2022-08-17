<?php

/** @noinspection SqlNoDataSourceInspection SqlResolve */

namespace A1DBox\Laravel\ModelAccessorBuilder\Tests\Feature;

use A1DBox\Laravel\ModelAccessorBuilder\Tests\Models\User;

it('generates trim(concat()) SQL', function () {
    $sql = <<<SQL
select *, (trim(concat("last_name", ' ', "name", ' ', "middle_name"))) AS "full_name_trim" from "users"
SQL;

    expect(User::query()
        ->withAccessor('full_name_trim')
        ->toSql()
    )->toBe($sql);
});

it('generates trim(concat()) SQL with qualified table', function () {
    $sql = <<<SQL
select *, (trim(concat("users"."last_name", ' ', "users"."name", ' ', "users"."middle_name"))) AS "full_name_trim_qualified" from "users"
SQL;

    expect(User::query()
        ->withAccessor('full_name_trim_qualified')
        ->toSql()
    )->toBe($sql);
});

it('does trim(concat()) on attributes', function () {
    expect(
        User::make([
            'last_name' => 'John',
            'name' => 'Doe',
            'middle_name' => '',
        ])->getAttribute('full_name_trim')
    )->toBe('John Doe');
});

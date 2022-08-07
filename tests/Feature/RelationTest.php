<?php

/** @noinspection SqlNoDataSourceInspection SqlResolve */

namespace A1DBox\Laravel\ModelAccessorBuilder\Tests\Feature;

use A1DBox\Laravel\ModelAccessorBuilder\Tests\Models\User;

it('generates SQL of relation aggregating json', function () {
    expect(User::query()
        ->withAccessor('tags_json')
        ->toSql()
    )->toBe(<<<SQL
select *, (select json_agg("users"."name") from "tags" where "tags"."user_id" = "users"."id" and "tags"."user_id" is not null) AS "tags_json" from "users"
SQL);
});

it('returns relation column with jsonAgg', function () {
    $model = User::make([
        'id' => 100,
    ])->fillTagsRelation();

    expect($model->getAttribute('tags_json'))->toBe(<<<JSON
["News","Animals","Politics","History"]
JSON);

    expect($model->getAttribute('tags_array'))->toBe([
        'News',
        'Animals',
        'Politics',
        'History'
    ]);
});

it('generates SQL of relation aggregating count', function () {
    $sql = <<<SQL
select *, (select count(*) from "tags" where "tags"."user_id" = "users"."id" and "tags"."user_id" is not null) AS "tags_custom_count" from "users"
SQL;

    expect(User::query()
        ->withAccessor('tags_custom_count')
        ->toSql()
    )->toBe($sql);
});

it('generates SQL of relation aggregating count into concat()', function () {
    $sql = <<<SQL
select *, (concat("name", ' ', "last_name", ' (tags count: ', (select count(*) from "tags" where "tags"."user_id" = "users"."id" and "tags"."user_id" is not null), ')')) AS "full_name_with_tags_count" from "users"
SQL;

    expect(User::query()
        ->withAccessor('full_name_with_tags_count')
        ->toSql()
    )->toBe($sql);
});

<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Relation\Query;
use A1DBox\Laravel\ModelAccessorBuilder\Model;
use Illuminate\Support\Str;

class RelationTestTag extends Model {
    protected $table = 'tags';

    protected $guarded = [];
}

class RelationTestUser extends Model {
    protected $table = 'users';

    protected $guarded = [];

    protected $attributes = [
        'name' => 'John',
        'last_name' => 'Doe',
    ];

    public function tags()
    {
        return $this->hasMany(RelationTestTag::class, 'user_id', 'id');
    }

    public function getTagsJsonAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet
                ->relation('tags', fn(Query $query) =>
                    $query->select(
                        $cabinet->jsonAgg($cabinet->col('name', $this))
                    )
                )
        );
    }

    public function getTagsArrayAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet
                ->relation('tags', fn(Query $query) =>
                    $query->select(
                        $cabinet->col('name', $this)
                    )
                )
        );
    }

    public function getTagsCustomCountAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet
                ->relation('tags', fn(Query $query) =>
                    $query->select(
                        $cabinet->countAgg()
                    )
                )
        );
    }
}

it('generates SQL of relation aggregating json', function () {
    $sql = <<<SQL
select *, (select json_agg("users"."name") from "tags" where "tags"."user_id" = "users"."id" and "tags"."user_id" is not null) AS "tags_json" from "users"
SQL;

    $model = new RelationTestUser;

    expect($sql)->toBe($model->newQuery()
        ->withAccessor('tags_json')
        ->toSql()
    );
});

it('returns relation column with jsonAgg', function () {
    $model = new RelationTestUser([
        'id' => 100,
    ]);
    $model->setRelation('tags', collect([
        ['id' => 1, 'user_id' => 100, 'name' => 'News'],
        ['id' => 1, 'user_id' => 100, 'name' => 'Animals'],
        ['id' => 1, 'user_id' => 100, 'name' => 'Politics'],
        ['id' => 1, 'user_id' => 100, 'name' => 'History'],
    ])->map([RelationTestTag::class, 'make']));

    expect($model->getAttribute('tags_json'))->toBe(<<<JSON
["News","Animals","Politics","History"]
JSON
    );

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

    $model = new RelationTestUser;

    expect($sql)->toBe($model->newQuery()
        ->withAccessor('tags_custom_count')
        ->toSql()
    );
});

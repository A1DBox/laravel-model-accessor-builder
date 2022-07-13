<?php

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Relation\Query;
use A1DBox\Laravel\ModelAccessorBuilder\Model;
use Illuminate\Support\Str;

class RelationTestTag extends Model {
    protected $table = 'tags';

    protected $fillable = [
        'name',
        'user_id'
    ];
}

class RelationTestUser extends Model {
    protected $table = 'users';

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
            static fn(BlueprintCabinet $cabinet) => $cabinet
                ->relation('tags', fn(Query $query) =>
                    $query->select(
                        $cabinet->jsonAgg('name')
                    )
                )
        );
    }
}

it('generates relation SQL', function () {
    $sql = <<<SQL
select *, (select json_agg("name") from "tags" where "tags"."user_id" = "users"."id" and "tags"."user_id" is not null) AS "tags_json" from "users"
SQL;

    $model = new RelationTestUser;
    $except = $model->newQuery()
        ->withAccessor('tags_json')
        ->toSql();
    dd($except, 123);
    expect($sql)->toBe($except);
});

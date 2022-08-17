<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Tests\Models;

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Expressions\Relation\Query;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $booking_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Tag[] $tags
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class User extends Model
{
    protected $table = 'users';

    protected $guarded = [];

    public function tags()
    {
        return $this->hasMany(Tag::class, 'user_id', 'id');
    }

    public function getTagsJsonAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet->relation(
                'tags',
                fn(Query $query) => $query->select(
                    $cabinet->jsonAgg($cabinet->col('name', $this))
                )
            )
        );
    }

    public function getTagsArrayAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet->relation(
                'tags',
                fn(Query $query) => $query->select(
                    $cabinet->col('name', $this)
                )
            )
        );
    }

    public function getTagsCustomCountAttribute()
    {
        return AccessorBuilder::make(
            static fn(BlueprintCabinet $cabinet) => $cabinet->relation(
                'tags',
                fn(Query $query) => $query->select(
                    $cabinet->countAgg()
                )
            )
        );
    }

    public function getFullNameAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet->concat(
                $cabinet->col('last_name'),
                $cabinet->str(' '),
                $cabinet->col('name'),
                $cabinet->str(' '),
                $cabinet->col('middle_name'),
            )
        );
    }

    public function getFullNameTrimAttribute()
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

    public function getFullNameTrimQualifiedAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet->trim(
                $cabinet->concat(
                    $cabinet->col('last_name', $this),
                    $cabinet->str(' '),
                    $cabinet->col('name', 'users'),
                    $cabinet->str(' '),
                    $cabinet->col('middle_name', $this),
                )
            )
        );
    }

    /**
     * Expected result: "John Doe (tags count: 2)"
     *
     * @return AccessorBuilder
     */
    public function getFullNameWithTagsCountAttribute()
    {
        return AccessorBuilder::make(
            fn(BlueprintCabinet $cabinet) => $cabinet->concat(
                $cabinet->col('name'),
                $cabinet->str(' '),
                $cabinet->col('last_name'),
                $cabinet->str(' (tags count: '),
                $cabinet->relation(
                    'tags',
                    fn(Query $query) => $query->select(
                        $cabinet->countAgg()
                    )
                ),
                $cabinet->str(')'),
            )
        );
    }

    public function fillTagsRelation()
    {
        return $this->setRelation('tags', collect([
            ['id' => 1, 'user_id' => 100, 'name' => 'News'],
            ['id' => 2, 'user_id' => 100, 'name' => 'Animals'],
            ['id' => 3, 'user_id' => 100, 'name' => 'Politics'],
            ['id' => 4, 'user_id' => 100, 'name' => 'History'],
        ])->map([Tag::class, 'make']));
    }
}

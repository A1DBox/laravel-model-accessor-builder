<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints;

use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Relation\Query;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Eloquent\Relations\Relation AS EloquentRelation;

class Relation extends Blueprint
{
    protected $name;

    protected $callback;

    public function __construct(string $name, callable $callback)
    {
        $this->name = $name;

        $this->callback = $callback;
    }

    public function resolve(array $attributes)
    {
        /**
         * @var EloquentRelation
         */
        $relation = $this->accessorBuilder->getModel()->getRelation($this->name);

        dd(get_class($relation));
    }

    public function toSql()
    {
        $query = $this->getQuery();

        $this->applyBuilderConstraints($query);

        return $query->toSql();
    }

    public function getQuery(): Query
    {
        $model = $this->accessorBuilder->getModel();
        $model->setIncrementing(false);

        /**
         * @var EloquentRelation $relation
         */
        $relation = EloquentRelation::noConstraints(function() use ($model) {
            return $model->{$this->name}();
        });

        $query = new Query($relation);
        $query->prepare($this);

        call_user_func($this->callback, $query);

        return $query;
    }

    protected function applyBuilderConstraints(Query $query): void
    {
        $relation = $query->getRelation();
        $model = $relation->getParent();

        $originalAttributes = $model->getAttributes();

        if ($relation instanceof HasOneOrMany) {
            $model->setAttribute($relation->getLocalKeyName(), new Expression($this->grammar->wrap(
                $model->qualifyColumn($relation->getQualifiedParentKeyName())
            )));
        }

        $relation->addConstraints();

        $model->setRawAttributes($originalAttributes);
    }
}

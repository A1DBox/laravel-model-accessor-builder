<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Expressions;

use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Expressions\Relation\Query;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Relation extends BlueprintExpression
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
        $model = $this->accessorBuilder->getModel();
        $query = $this->getQuery($model);
        $result = $model->getRelationValue($this->name);

        if (!$result instanceof Collection) {
            throw new InvalidArgumentException(sprintf(
                'Relation "%s" must return collection instance for aggregating',
                $this->name
            ));
        }

        $select = $query->getSelect();

        $result->each(fn(Model $item) => $select->resolve($item->getAttributes()));

        return $select->resolveAggregate();
    }

    public function toSql()
    {
        $model = $this->accessorBuilder->getModel();
        $incrementing = $model->getIncrementing();
        $model->setIncrementing(false);

        $query = $this->getQuery($model);

        $this->applyBuilderConstraints($query);

        $model->setIncrementing($incrementing);

        return $query->toSql();
    }

    public function getQuery(Model $model): Query
    {
        /**
         * @var EloquentRelation $relation
         */
        $relation = EloquentRelation::noConstraints(function() use ($model) {
            return $model->{$this->name}();
        });

        $query = $this->make(new Query($relation));

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
                $relation->getQualifiedParentKeyName()
            )));
        }

        $relation->addConstraints();

        $model->setRawAttributes($originalAttributes);
    }
}

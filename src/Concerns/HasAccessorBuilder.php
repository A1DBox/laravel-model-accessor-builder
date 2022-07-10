<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Concerns;

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use UnexpectedValueException;

trait HasAccessorBuilder
{
    public function scopeWithAccessor(Builder $query, $accessors)
    {
        $accessors = Arr::wrap($accessors);

        $baseBuilder = $query->getQuery();

        if (empty($baseBuilder->columns)) {
            $query->select('*');
        }

        foreach ($accessors as $name) {
            if (!$this->hasGetMutator($name)) {
                throw new UnexpectedValueException(sprintf(
                    'Accessor %s not found in model %s',
                    $name,
                    static::class
                ));
            }

            $accessor = parent::mutateAttribute($name, null);

            if (!$accessor instanceof AccessorBuilder) {
                throw new UnexpectedValueException(sprintf(
                    'Accessor %s not instanceof ' . AccessorBuilder::class,
                    $name,
                    static::class
                ));
            }

            $query->addSelect($baseBuilder->getConnection()->raw(sprintf(
                '(%s) AS %s',
                $accessor->toSql(),
                $query->getGrammar()->wrap($name)
            )));
        }
    }

    protected function mutateAttribute($key, $value)
    {
        $accessor = parent::mutateAttribute($key, $value);

        if ($accessor instanceof AccessorBuilder) {
            if ($accessor->hasOriginalValue()) {
                return $accessor->getOriginalValue();
            }

            $accessor = $accessor->resolveModelValue();
        }

        return $accessor;
    }
}

<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Concerns;

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
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
            $accessor = $this->getAttributeAccessorBuilder($name);

            $query->addSelect($baseBuilder->getConnection()->raw(sprintf(
                '(%s) AS %s',
                $accessor->toSql(),
                $query->getGrammar()->wrap($name)
            )));
        }
    }

    public function getAttributeAccessorBuilder($key)
    {
        if (!$this->hasGetMutator($key)) {
            throw new UnexpectedValueException(sprintf(
                'Accessor %s not found in model %s',
                $key,
                static::class
            ));
        }

        $accessor = parent::mutateAttribute($key, null);

        if (!$accessor instanceof AccessorBuilder) {
            throw new UnexpectedValueException(sprintf(
                'Accessor %s not instanceof ' . AccessorBuilder::class,
                $key,
                static::class
            ));
        }

        $accessor->setModel($this);

        return $accessor;
    }

    protected function mutateAttribute($key, $value)
    {
        try {
            $accessor = $this->getAttributeAccessorBuilder($key);

            $attributeExists = array_key_exists(
                $key,
                $this->getAttributes()
            );

            if ($attributeExists) {
                return $value;
            }

            return $accessor->resolveModelValue();
        } catch (UnexpectedValueException $e) {
        }

        return parent::mutateAttribute($key, $value);
    }
}

<?php

namespace A1DBox\Laravel\ModelAccessorBuilder;

use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilder\BlueprintCabinet;
use A1DBox\Laravel\ModelAccessorBuilder\Blueprints\Blueprint;
use A1DBox\Laravel\ModelAccessorBuilder\Contracts\ExpressionGrammar;
use A1DBox\Laravel\ModelAccessorBuilder\Query\Grammar\MariaDbGrammar;
use A1DBox\Laravel\ModelAccessorBuilder\Query\Grammar\MySqlGrammar;
use A1DBox\Laravel\ModelAccessorBuilder\Query\Grammar\PostgresGrammar;
use A1DBox\Laravel\ModelAccessorBuilder\Query\Grammar\SQLiteGrammar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PDO;
use RuntimeException;

class AccessorBuilder
{
    protected BlueprintCabinet $blueprintCabinet;

    protected Model $model;

    protected $query;

    protected $grammar;

    protected $resolver;

    protected bool $isQuerySetup = false;

    public function __construct(Model $model, callable $resolver)
    {
        $this->model = $model;
        $this->resolver = $resolver;
        $this->blueprintCabinet = new BlueprintCabinet($this);
    }

    public static function make(Model $model, callable $resolver)
    {
        return new static($model, $resolver);
    }

    protected function setupQuery()
    {
        if (!$this->isQuerySetup) {
            $this->query = $this->model->newQuery();

            $this->isQuerySetup = true;
        }
    }

    public function toSql()
    {
        return $this->executeResolver()->toSql();
    }

    public function getGrammar()
    {
        $this->setupQuery();

        if (isset($this->grammar)) {
            return $this->grammar;
        }

        $connection = $this->query->getConnection();
        $driver = $connection->getDriverName();

        /**
         * @var ExpressionGrammar $grammar
         */
        switch ($driver) {
            case 'mysql':
                $version = $connection->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);

                $grammar = Str::contains($version, 'MariaDB')
                    ? new MariaDbGrammar
                    : new MySqlGrammar;

                $grammar = $connection->withTablePrefix($grammar);
                break;
            case 'pgsql':
                $grammar = $connection->withTablePrefix(
                    new PostgresGrammar
                );
                break;
            case 'sqlite':
                $grammar = $connection->withTablePrefix(
                    new SQLiteGrammar
                );
                break;
        }

        if (!isset($grammar)) {
            throw new RuntimeException('This database is not supported.');
        }

        $grammar->setConnection($this->getConnection());

        return $this->grammar = $grammar;
    }

    public function getConnection()
    {
        $this->setupQuery();

        return $this->query->getConnection();
    }

    public function resolveModelValue()
    {
        return $this->executeResolver()->resolve($this->model->getAttributes());
    }

    protected function executeResolver(): Blueprint
    {
        return call_user_func($this->resolver, $this->blueprintCabinet, $this);
    }
}

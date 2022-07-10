<?php

namespace A1DBox\Laravel\ModelAccessorBuilder\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use A1DBox\Laravel\ModelAccessorBuilder\AccessorBuilderServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            AccessorBuilderServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}

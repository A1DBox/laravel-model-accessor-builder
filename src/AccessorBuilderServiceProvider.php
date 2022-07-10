<?php

namespace A1DBox\Laravel\ModelAccessorBuilder;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AccessorBuilderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-model-accessor-builder');
    }
}

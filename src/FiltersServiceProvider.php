<?php

declare(strict_types=1);

namespace Worksome\Filters;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Filters\Commands\FiltersCommand;

class FiltersServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-filters')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-filters_table')
            ->hasCommand(FiltersCommand::class);
    }
}

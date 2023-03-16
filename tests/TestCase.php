<?php

namespace Worksome\Filters\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Worksome\Filters\FiltersServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Worksome\\Filters\\Database\\Factories\\' . class_basename(
                $modelName
            ) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            FiltersServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-filters_table.php.stub';
        $migration->up();
        */
    }
}

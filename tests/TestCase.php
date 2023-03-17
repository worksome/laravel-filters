<?php

declare(strict_types=1);

namespace Worksome\Filters\Tests;

use AllowDynamicProperties;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use Worksome\Filters\Tests\database\seeders\DatabaseSeeder;

#[AllowDynamicProperties]
abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function defineDatabaseSeeders(): void
    {
        $this->seed(DatabaseSeeder::class);
    }
}

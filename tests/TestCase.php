<?php

namespace Worksome\Filters\Tests;

use AllowDynamicProperties;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase as Orchestra;
use Worksome\Filters\Tests\Fake\TestModel;

#[AllowDynamicProperties]
abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations()
    {
        $schema = $this->app->make(Builder::class);

        $schema->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('even');
            $table->dateTime('non_sortable');
            $table->timestamps();
            $table->softDeletes();
        });

        Collection::make([
            ['name' => 'four', 'even' => 1],
            ['name' => 'three', 'even' => 0],
            ['name' => 'six', 'even' => 1],
            ['name' => 'five', 'even' => 0],
            ['name' => 'two', 'even' => 1],
            ['name' => 'seven', 'even' => 0],
            ['name' => 'nine', 'even' => 0],
            ['name' => 'eight', 'even' => 1],
            ['name' => 'ten', 'even' => 1],
            ['name' => 'one', 'even' => 0],
            ['name' => 'eleven', 'even' => 1, 'deleted_at' => new Carbon()],
            ['name' => 'twelve', 'even' => 0, 'deleted_at' => new Carbon()],
        ])->each(fn (array $data, int $key) => TestModel::query()->create([
            ... $data,
            'created_at' => Carbon::now()->addSeconds(10 * $key),
            'updated_at' => Carbon::now()->addSeconds(15 * $key),
            'non_sortable' => Carbon::now()->addSeconds(20 * $key),
        ]));
    }
}

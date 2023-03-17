<?php

declare(strict_types=1);

namespace Worksome\Filters\Tests\database\seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Worksome\Filters\Tests\Fake\TestModel;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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

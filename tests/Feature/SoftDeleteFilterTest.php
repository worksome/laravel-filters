<?php

declare(strict_types=1);

namespace Worksome\Filters\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Worksome\Filters\FilterQuery;
use Worksome\Filters\Tests\Fake\TestModel;
use Worksome\Filters\Tests\Fake\TestModelFilter;

it('can filter soft deleted by default', function () {
    $filtered = (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input([])
        ->get();

    expect($filtered)->toHaveCount(10);
});

it('can filter with trashed', function () {
    $filtered = (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input(['include_trashed' => true])
        ->get();

    expect($filtered)->toHaveCount(12);
});

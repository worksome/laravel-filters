<?php

declare(strict_types=1);

namespace Worksome\Filters\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Worksome\Filters\FilterQuery;
use Worksome\Filters\Tests\Fake\TestModel;
use Worksome\Filters\Tests\Fake\TestModelFilter;

it('can handle default sorting', function () {
    $filtered = (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input([
            'sortBy' => ['created_at' => 'desc'],
        ])
        ->get();

    expect($filtered->first()->created_at->timestamp)->toBeGreaterThan($filtered->get(2)->created_at->timestamp);
});

it('can sort using a dedicated sort method', function () {
    $filtered = (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input([
            'sortBy' => ['customName' => 'asc'],
        ])
        ->get();

    expect($filtered->pluck('name')->toArray())->toEqual(TestModelFilter::CUSTOMER_NAME_ORDER);
});

it('does not sort non-sortable columns', function () {
    $filtered = (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input([
            'sortBy'          => ['non_sortable' => 'desc'],
        ])
        ->get();

    $collection_sorted = $filtered
        ->sortByDesc(fn (TestModel $model) => $model->non_sortable->timestamp)
        ->values();

    expect($filtered->pluck('name')->toArray())->not->toEqual($collection_sorted->pluck('name')->toArray());
});

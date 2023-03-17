<?php

declare(strict_types=1);

namespace Worksome\Filters\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Builder;
use Worksome\Filters\Exceptions\MissingRequiredFilterException;
use Worksome\Filters\Exceptions\MissingRequiredModelOrQueryException;
use Worksome\Filters\FilterQuery;
use Worksome\Filters\Tests\Fake\SecondTestModelFilter;
use Worksome\Filters\Tests\Fake\TestModel;
use Worksome\Filters\Tests\Fake\TestModelFilter;

it('can filter an Eloquent model', function () {
    $filtered = (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input([
            'name' => 'one',
        ])
        ->get();

    expect($filtered)
        ->toHaveCount(1)
        ->first()->toEqual(TestModel::where('name', 'one')->first());
});

it('can paginate the filter', function () {
    $filtered = (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input([
            'even' => 1,
        ])
        ->paginateFilter(2);

    expect($filtered)->toHaveCount(2);
});

it('can pass a query builder instead of a model class', function () {
    $query = TestModel::query()->limit(2);

    $filtered = (new FilterQuery($this->app->make(Repository::class)))
        ->query($query)
        ->apply(TestModelFilter::class)
        ->input([
            'even' => 1,
        ])
        ->get();

    expect($filtered)->toHaveCount(2);
});

it('can simple paginate the filter', function () {
    $filtered = (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input([
            'even' => 1,
        ])
        ->simplePaginateFilter(2);

    expect($filtered)->toHaveCount(2);
});

it('returns the underlying query builder', function () {
    $query = (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input([
            'name' => 'one',
        ])
        ->getQuery();

    expect($query)->toBeInstanceOf(Builder::class);
});

it('has the same response for the get() method and the query builder get() method', function () {
    $filter = (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input([
            'name' => 'one',
        ]);

    expect($filter->getQuery()->get())->toEqual($filter->get());
});

it('throws a missing required filter exception if not filter is provided', function () {
    (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->input([
            'name' => 'one',
        ])
        ->get();
})->throws(MissingRequiredFilterException::class);

it('throws a missing required model or query exception if no model is provided', function () {
    (new FilterQuery($this->app->make(Repository::class)))
        ->apply(TestModelFilter::class)
        ->input([
            'name' => 'one',
        ])
        ->get();
})->throws(MissingRequiredModelOrQueryException::class);

it('can reset the model on filter class change', function () {
    $query = TestModel::query()->limit(2);

    $filter = (new FilterQuery($this->app->make(Repository::class)))
        ->query($query)
        ->apply(TestModelFilter::class)
        ->input([
            'even' => 1,
        ]);

    $filter->get();

    expect($filter->getQuery())->toEqual($query);

    $filter->apply(TestModelFilter::class);

    expect($filter->getQuery())->toEqual($query);

    $filter->apply(SecondTestModelFilter::class);
});

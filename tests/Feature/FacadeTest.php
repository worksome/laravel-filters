<?php

declare(strict_types=1);

namespace Worksome\Filters\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Worksome\Filters\Filter;
use Worksome\Filters\FilterQuery;
use Worksome\Filters\Tests\Fake\TestModel;
use Worksome\Filters\Tests\Fake\TestModelFilter;

it('returns an instance of filter query', function () {
    expect(Filter::model('dummy'))->toBeInstanceOf(FilterQuery::class);
});

it('can be called statically', function () {
    $input = ['name' => 'one'];

    $filteredInstance = (new FilterQuery($this->app->make(Repository::class)))
        ->model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input($input)
        ->get();

    $filteredStatic = Filter::model(TestModel::class)
        ->apply(TestModelFilter::class)
        ->input($input)
        ->get();

    expect($filteredStatic)->toEqual($filteredInstance);
});

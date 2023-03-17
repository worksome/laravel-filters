<?php

namespace Worksome\Filters\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Builder;
use Worksome\Filters\Exceptions\MissingRequiredFilterException;
use Worksome\Filters\Exceptions\MissingRequiredModelOrQueryException;
use Worksome\Filters\FilterQuery;
use Worksome\Filters\Tests\Fake\SecondTestModelFilter;
use Worksome\Filters\Tests\Fake\TestModel;
use Worksome\Filters\Tests\Fake\TestModelFilter;
use Worksome\Filters\Tests\TestCase;

class FilterQueryTest extends TestCase
{
    /** @test */
    public function it_filters_an_eloquent_model()
    {
        $filtered = (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input([
                'name' => 'one',
            ])
            ->get();

        $this->assertCount(1, $filtered);
        $this->assertEquals(TestModel::where('name', 'one')->first(), $filtered->first());
    }

    /** @test */
    public function it_can_paginate_the_filter()
    {
        $filtered = (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input([
                'even' => 1,
            ])
            ->paginateFilter(2);
        $this->assertCount(2, $filtered);
    }

    /** @test */
    public function a_query_builder_can_be_passed_instead_of_model_class()
    {
        $query = TestModel::query()->limit(2);

        $filtered = (new FilterQuery($this->app->make(Repository::class)))
            ->query($query)
            ->apply(TestModelFilter::class)
            ->input([
                'even' => 1,
            ])
            ->get();
        $this->assertCount(2, $filtered);
    }

    /** @test */
    public function it_can_simple_paginate_the_filter()
    {
        $filtered = (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input([
                'even' => 1,
            ])
            ->simplePaginateFilter(2);
        $this->assertCount(2, $filtered);
    }

    /** @test */
    public function it_returns_the_underlying_query_builder()
    {
        $query = (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input([
                'name' => 'one',
            ])
            ->getQuery();
        $this->assertInstanceOf(Builder::class, $query);
    }

    /** @test */
    public function the_get_method_is_the_same_as_query_builder_get()
    {
        $filter = (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input([
                'name' => 'one',
            ]);

        $this->assertEquals($filter->getQuery()->get(), $filter->get());
    }

    /** @test */
    public function it_throws_a_missing_required_filter_exception_if_no_filter_is_provided()
    {
        $this->expectException(MissingRequiredFilterException::class);
        (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->input([
                'name' => 'one',
            ])
            ->get();
    }

    /** @test */
    public function it_throws_a_missing_required_model_or_query_exception_if_no_model_is_provided()
    {
        $this->expectException(MissingRequiredModelOrQueryException::class);

        (new FilterQuery($this->app->make(Repository::class)))
            ->apply(TestModelFilter::class)
            ->input([
                'name' => 'one',
            ])
            ->get();
    }

    /** @test */
    public function it_resets_the_query_on_filter_class_change()
    {
        $query = TestModel::query()->limit(2);

        $filter = (new FilterQuery($this->app->make(Repository::class)))
            ->query($query)
            ->apply(TestModelFilter::class)
            ->input([
                'even' => 1,
            ]);
        $filter->get();
        $this->assertEquals($query, $filter->getQuery());

        $filter->apply(TestModelFilter::class);
        $this->assertEquals($query, $filter->getQuery());
        $filter->apply(SecondTestModelFilter::class);
    }
}

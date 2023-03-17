<?php

namespace Worksome\Filters\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Facade;
use Worksome\Filters\Filter;
use Worksome\Filters\FilterQuery;
use Worksome\Filters\Tests\Fake\TestModel;
use Worksome\Filters\Tests\Fake\TestModelFilter;
use Worksome\Filters\Tests\TestCase;

class FacadeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Facade::setFacadeApplication([FilterQuery::class => new FilterQuery($this->app->make(Repository::class))]);
    }

    /** @test */
    public function it_returns_an_instance_of_filter_query()
    {
        $this->assertInstanceOf(FilterQuery::class, Filter::model('dummy'));
    }

    /** @test */
    public function it_can_be_called_statically()
    {
        $input = ['name' => 'one'];
        $filtered_instance = $filtered = (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input($input)
            ->get();
        $filtered_static = Filter::model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input($input)
            ->get();

        $this->assertEquals($filtered_instance, $filtered_static);
    }
}

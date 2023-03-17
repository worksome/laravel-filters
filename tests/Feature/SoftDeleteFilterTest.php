<?php

namespace Worksome\Filters\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Worksome\Filters\FilterQuery;
use Worksome\Filters\Tests\Fake\TestModel;
use Worksome\Filters\Tests\Fake\TestModelFilter;
use Worksome\Filters\Tests\TestCase;

class SoftDeleteFilterTest extends TestCase
{
    /** @test */
    public function it_filters_soft_deleted_by_default()
    {
        $filtered = (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input([])
            ->get();

        $this->assertCount(10, $filtered);
    }

    /** @test */
    public function it_can_filter_with_trashed()
    {
        $filtered = (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input(['include_trashed' => true])
            ->get();

        $this->assertCount(12, $filtered);
    }
}

<?php

namespace Worksome\Filters\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Worksome\Filters\FilterQuery;
use Worksome\Filters\Tests\Fake\TestModel;
use Worksome\Filters\Tests\Fake\TestModelFilter;
use Worksome\Filters\Tests\TestCase;

class SortableFilterTest extends TestCase
{
    /** @test */
    public function it_handle_default_sorting()
    {
        $filtered = (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input([
                'sortBy' => ['created_at' => 'desc'],
            ])
            ->get();

        $this->assertGreaterThan($filtered->get(2)->created_at->timestamp, $filtered->first()->created_at->timestamp);
    }

    /** @test */
    public function it_can_be_sorted_by_dedicated_sort_method()
    {
        $filtered = (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input([
                'sortBy' => ['customName' => 'asc'],
            ])
            ->get();

        $this->assertEquals(TestModelFilter::CUSTOMER_NAME_ORDER, $filtered->pluck('name')->toArray());
    }

    /** @test */
    public function it_does_not_sort_non_sortable_columns()
    {
        $filtered = (new FilterQuery($this->app->make(Repository::class)))
            ->model(TestModel::class)
            ->apply(TestModelFilter::class)
            ->input([
                'sortBy'          => ['non_sortable' => 'desc'],
            ])
            ->get();

        $collection_sorted = $filtered
            ->sortByDesc(fn(TestModel $model) => $model->non_sortable->timestamp)
            ->values();

        $this->assertNotEquals($collection_sorted->pluck('name')->toArray(), $filtered->pluck('name')->toArray());
    }
}

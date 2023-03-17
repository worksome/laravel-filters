<?php

namespace Worksome\Filters;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for Worksome\Filters\FilterQuery
 *
 * @method static FilterQuery model(string $modelClass)
 * @method static FilterQuery apply(string $filterClass)
 * @method static FilterQuery input(array $input = [])
 * @method static FilterQuery query(Builder $query)
 * @method static Builder getQuery
 * @method static \Illuminate\Support\Collection|static[] get
 * @method static LengthAwarePaginator paginateFilter($perPage = null, $columns = ['*'], $pageName = 'page', $page =null)
 * @method static Paginator            simplePaginateFilter($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
 *
 */
class Filter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FilterQuery::class;
    }
}

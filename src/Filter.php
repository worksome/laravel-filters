<?php

declare(strict_types=1);

namespace Worksome\Filters;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static FilterQuery<TModel, ModelFilter>        model<TModel of Model>(class-string<TModel> $modelClass)
 * @method static FilterQuery<Model, TFilter>             apply<TFilter of ModelFilter>(class-string<TFilter> $filterClass)
 * @method static FilterQuery                             input(array $input = [])
 * @method static FilterQuery<TModel, ModelFilter>        query<TModel of Model>(Builder<TModel> $query)
 * @method static Builder                                 getQuery()
 * @method static \Illuminate\Support\Collection|static[] get()
 * @method static LengthAwarePaginator                    paginateFilter($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
 * @method static Paginator                               simplePaginateFilter($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
 *
 * @see FilterQuery
 */
class Filter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FilterQuery::class;
    }
}

<?php

declare(strict_types=1);

namespace Worksome\Filters;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Worksome\Filters\Exceptions\MissingRequiredFilterException;
use Worksome\Filters\Exceptions\MissingRequiredModelOrQueryException;

/**
 * Our own implementation for filtering based on `EloquentFilter\Filterable`, but without the usage of scopes.
 *
 * @template TModel of Model
 * @template TFilter of ModelFilter
 */
class FilterQuery
{
    /** @var array */
    protected array $filtered = [];

    /** @var array<int|string, mixed> */
    protected array $input = [];

    protected Repository $config;

    /** @var class-string<TModel>|null */
    private string|null $modelClass = null;

    /** @var class-string<TFilter>|null */
    private string|null $filterClass = null;

    /** @var Builder<TModel>|null */
    private Builder|null $query = null;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * @param  class-string<TModel>  $modelClass
     *
     * @return self<TModel, TFilter>
     */
    public function model(string $modelClass): self
    {
        $this->modelClass = $modelClass;
        $this->resetQuery();

        return $this;
    }

    /**
     * @param  class-string<TFilter>  $filterClass
     *
     * @return self<TModel, TFilter>
     */
    public function apply(string $filterClass): self
    {
        if ($this->filterClass && $this->filterClass != $filterClass) {
            $this->resetQuery();
        }

        $this->filterClass = $filterClass;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $input
     *
     * @return self<TModel, TFilter>
     */
    public function input(array $input = []): self
    {
        $this->input = $input;

        return $this;
    }

    /**
     * @param  Builder<TModel>  $query
     *
     * @return self<TModel, TFilter>
     */
    public function query(Builder $query): self
    {
        $this->query = $query;

        return $this;
    }

    /** @return Builder<TModel> */
    public function getQuery(): Builder
    {
        if (! isset($this->query)) {
            $this->query = $this->getModelClass()::query();
        }

        $this->run();

        return $this->query;
    }

    /**
     * @return Collection<int, TModel>
     *
     * @throws MissingRequiredFilterException|MissingRequiredModelOrQueryException
     */
    public function get(): Collection
    {
        return $this->getQuery()->get();
    }

    /**
     * @param  array<string>  $columns
     *
     * @return LengthAwarePaginator<TModel>
     *
     * @throws MissingRequiredFilterException|MissingRequiredModelOrQueryException
     */
    public function paginateFilter(
        int|null $perPage = null,
        array $columns = ['*'],
        string $pageName = 'page',
        int|null $page = null,
    ): LengthAwarePaginator {
        $perPage = $perPage ?: $this->config->get('eloquentfilter.paginate_limit');
        assert($perPage === null || is_int($perPage));
        $paginator = $this->getQuery()->paginate($perPage, $columns, $pageName, $page);
        $paginator->appends($this->filtered);

        return $paginator;
    }

    /**
     * @param  array<int, string>  $columns
     *
     * @return Paginator<TModel>
     *
     * @throws MissingRequiredFilterException|MissingRequiredModelOrQueryException
     */
    public function simplePaginateFilter(
        int|null $perPage = null,
        array $columns = ['*'],
        string $pageName = 'page',
        int|null $page = null,
    ): Paginator {
        $perPage = $perPage ?: $this->config->get('eloquentfilter.paginate_limit');
        assert($perPage === null || is_int($perPage));
        $paginator = $this->getQuery()->simplePaginate($perPage, $columns, $pageName, $page);
        $paginator->appends($this->filtered);

        return $paginator;
    }

    /** @return self<TModel, TFilter> */
    protected function run(): self
    {
        $filter = $this->provideFilter();

        $modelFilter = new $filter($this->query, $this->input);

        // @phpstan-ignore-next-line
        $this->filtered = $modelFilter->input();

        $modelFilter->handle();

        return $this;
    }

    /** @return class-string<TModel> */
    private function getModelClass(): string
    {
        if (! isset($this->modelClass)) {
            throw new MissingRequiredModelOrQueryException('Filter is missing required eloquent Model class.');
        }

        return $this->modelClass;
    }

    /** @return class-string<TFilter>|class-string<ModelFilter> */
    private function provideFilter(): string
    {
        if ($this->filterClass) {
            return $this->filterClass;
        }

        $namespace = $this->config->get(
            'eloquentfilter.namespace',
            'App\\ModelFilters\\'
        );

        assert(is_string($namespace));

        $filter = sprintf('%s%sFilter', $namespace, class_basename($this));

        if (! class_exists($filter)) {
            throw new MissingRequiredFilterException();
        }

        assert(is_subclass_of($filter, ModelFilter::class));

        return $filter;
    }

    private function resetQuery(): void
    {
        $this->query = null;
    }
}

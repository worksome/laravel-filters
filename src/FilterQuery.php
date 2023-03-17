<?php

namespace Worksome\Filters;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Worksome\Filters\Exceptions\MissingRequiredFilterException;
use Worksome\Filters\Exceptions\MissingRequiredModelOrQueryException;

/** Our own implementation for filtering based on `EloquentFilter\Filterable`, but without the usage of scopes. */
class FilterQuery
{
    /** @var array<int, string> */
    protected array $filtered = [];

    /** @var array<int, mixed> */
    protected array $input = [];

    protected Repository $config;

    private string|null $modelClass = null;

    private string|null $filterClass = null;

    private Builder|null $query = null;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function model(string $modelClass): self
    {
        $this->modelClass = $modelClass;
        $this->resetQuery();

        return $this;
    }

    public function apply(string $filterClass): self
    {
        if ($this->filterClass && $this->filterClass != $filterClass) {
            $this->resetQuery();
        }
        $this->filterClass = $filterClass;

        return $this;
    }

    /** @param array<string, mixed> $input */
    public function input(array $input = []): self
    {
        $this->input = $input;
        return $this;
    }

    public function query(Builder $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function getQuery(): Builder
    {
        if (! isset($this->query)) {
            $this->query = $this->getModelClass()::query();
        }

        $this->run();

        return $this->query;
    }

    /**
     * @return Builder[]|Collection
     *
     * @throws MissingRequiredFilterException|MissingRequiredModelOrQueryException
     */
    public function get()
    {
        return $this->getQuery()->get();
    }

    /**
     * @param array<string> $columns
     *
     * @throws MissingRequiredFilterException|MissingRequiredModelOrQueryException
     */
    public function paginateFilter(
        ?int $perPage = null,
        array $columns = ['*'],
        string $pageName = 'page',
        ?int $page = null,
    ): LengthAwarePaginator {
        $perPage = $perPage ?: $this->config->get('eloquentfilter.paginate_limit');
        $paginator = $this->getQuery()->paginate($perPage, $columns, $pageName, $page);
        $paginator->appends($this->filtered);

        return $paginator;
    }

    /**
     * @param array<int, string> $columns
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
        $paginator = $this->getQuery()->simplePaginate($perPage, $columns, $pageName, $page);
        $paginator->appends($this->filtered);

        return $paginator;
    }

    protected function run(): self
    {
        $filter = $this->provideFilter();

        $modelFilter = new $filter($this->query, $this->input);

        $this->filtered = $modelFilter->input();

        $modelFilter->handle();

        return $this;
    }

    private function getModelClass(): string
    {
        if (! isset($this->modelClass)) {
            throw new MissingRequiredModelOrQueryException('Filter is missing required eloquent Model class.');
        }
        return $this->modelClass;
    }

    private function provideFilter(): string
    {
        if ($this->filterClass) {
            return $this->filterClass;
        }

        $filter = $this->config->get(
            'eloquentfilter.namespace',
            'App\\ModelFilters\\'
        ) . class_basename($this) . 'Filter';

        if (class_exists($filter)) {
            return $filter;
        }

        throw new MissingRequiredFilterException();
    }

    private function resetQuery(): void
    {
        $this->query = null;
    }
}

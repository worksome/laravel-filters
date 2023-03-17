<?php

namespace Worksome\Filters;

use EloquentFilter\ModelFilter as BaseModelFilter;
use Illuminate\Support\Str;

/** This class wraps around the `EloquentFilter\ModelFilter` to add sorting functionality and `IncludeTrash` helper. */
class ModelFilter extends BaseModelFilter
{
    /**
     * Array of fields available for sorting.
     *
     * @var array<int, string>
     */
    protected array $sortable = [];

    /**
     * Array of fields available for sorting by default.
     *
     * @var array<int, string>
     */
    protected array $defaultSortable = ['id', 'created_at', 'updated_at'];

    protected string|null $modelTable = null;

    /** Check whether a column is in the `$sortable` array. */
    public function isSortable(string $column): bool
    {
        return in_array($column, array_merge($this->defaultSortable, $this->sortable));
    }

    /**
     * Sort the Items by column/direction.
     * [ [ $column => $direction ], ... ]
     *
     * Non-sortable columns will not be applied.
     *
     * @param array<string, string> $sorts
     */
    public function sortBy(array $sorts): void
    {
        foreach ($sorts as $column => $direction) {
            // Check if pre-defined orderBy method exists to sort by
            if (method_exists($this, $method = 'sortBy' . Str::studly($column))) {
                $this->$method($direction);
                continue;
            }

            // Otherwise order by the column in a specific direction, default DESC
            if ($this->isSortable($column)) {
                $this->orderBy($column, $direction);
                continue;
            }
        }
    }

    public function includeTrashed(bool $value): void
    {
        if ($value) {
            $this->withTrashed();
        }
    }

    protected function prefixWithModelTable(string $field): string
    {
        if (! $this->modelTable) {
            return $field;
        }

        return "{$this->modelTable}.{$field}";
    }
}

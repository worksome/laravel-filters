<?php

namespace Worksome\Filters\Tests\Fake;

use Worksome\Filters\ModelFilter;

class TestModelFilter extends ModelFilter
{
    public const CUSTOMER_NAME_ORDER = ['one', 'three', 'two', 'four', 'six', 'five', 'seven', 'nine', 'ten', 'eight'];

    public function name(string $name)
    {
        $this->where('name', $name);
    }

    public function sortByCustomName(string $direction)
    {
        $order = collect(self::CUSTOMER_NAME_ORDER)
            ->map(fn ($name, $index) => "WHEN '{$name}' THEN {$index}")
            ->implode(' ');
        $this->orderByRaw("CASE name {$order} END ");
    }
}

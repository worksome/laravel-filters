<?php

declare(strict_types=1);

namespace Worksome\Filters\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Worksome\Filters\Filters
 */
class Filters extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-filters';
    }
}

<?php

namespace Worksome\Filters\Tests\Fake;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Worksome\Filters\FilterQuery;

class FilterQueryFake extends FilterQuery
{
    public function __construct(Application $container)
    {
        parent::__construct(
            $container->make(Repository::class),
        );
    }
}

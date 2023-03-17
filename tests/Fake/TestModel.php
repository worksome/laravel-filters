<?php

declare(strict_types=1);

namespace Worksome\Filters\Tests\Fake;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestModel extends Model
{
    use SoftDeletes;

    protected $casts = [
        'non_sortable' => 'datetime',
    ];

    protected $guarded = [];
}

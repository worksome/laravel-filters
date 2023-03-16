<?php

declare(strict_types=1);

namespace Worksome\Filters\Commands;

use Illuminate\Console\Command;

class FiltersCommand extends Command
{
    public $signature = 'laravel-filters';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace Worksome\Filters\Exceptions;

use Exception;

/**
 * Exception thrown by `Worksome\Filters\FilterQuery` when a required FilterClass property is not provided to it.
 */
class MissingRequiredFilterException extends Exception
{
}

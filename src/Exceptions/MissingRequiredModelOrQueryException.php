<?php

declare(strict_types=1);

namespace Worksome\Filters\Exceptions;

use Exception;

/**
 * Exception thrown by Worksome\Filters\ModelQuery when no ModelClass or Query property is provided
 */
class MissingRequiredModelOrQueryException extends Exception
{
}

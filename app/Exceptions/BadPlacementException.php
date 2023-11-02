<?php

namespace App\Exceptions;

use Exception;

class BadPlacementException extends Exception
{
    const DEFAULT_MESSAGE = 'Bad ship placement';

    public function __construct(string $message = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, 422);
    }
}

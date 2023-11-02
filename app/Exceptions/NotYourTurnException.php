<?php

namespace App\Exceptions;

use Exception;

class NotYourTurnException extends Exception
{
    const DEFAULT_MESSAGE = 'It is not your turn!';

    public function __construct(string $message = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, 422);
    }
}

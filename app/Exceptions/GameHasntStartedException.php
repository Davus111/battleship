<?php

namespace App\Exceptions;

use Exception;

class GameHasntStartedException extends Exception
{
    const DEFAULT_MESSAGE = "Game hasn't started yet";

    public function __construct(string $message = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, 422);
    }
}

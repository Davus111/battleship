<?php

namespace App\Exceptions;

use Exception;

class NotAllShipsSetException extends Exception
{
    const DEFAULT_MESSAGE = 'Not every player has set their battleships';

    public function __construct(string $message = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, 422);
    }
}

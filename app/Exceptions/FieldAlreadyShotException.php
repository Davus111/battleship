<?php

namespace App\Exceptions;

use Exception;

class FieldAlreadyShotException extends Exception
{
    const DEFAULT_MESSAGE = 'You already shot here, choose another field';

    public function __construct(string $message = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, 422);
    }
}

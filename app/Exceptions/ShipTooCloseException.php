<?php

namespace App\Exceptions;

use Exception;

class ShipTooCloseException extends Exception
{
    const DEFAULT_MESSAGE = 'Ships can not be next to each otherd';

    public function __construct(string $message = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, 422);
    }
}

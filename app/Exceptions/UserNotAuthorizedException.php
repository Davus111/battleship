<?php

namespace App\Exceptions;

use Exception;

class UserNotAuthorizedException extends Exception
{
    const DEFAULT_MESSAGE = 'Only owner can make this action!';

    public function __construct(string $message = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, 401);
    }
}
